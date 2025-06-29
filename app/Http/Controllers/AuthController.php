<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user  = User::where('email', $request->email)->first();

            if (empty($user->email_verified_at)) {
                throw new \Exception('É necessário ativar sua conta primeiro!');
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['status' => false, 'message' => 'Crendenciais inválidas'], 400);
    }

    public function register(Request $request)
    {
        try {
            $request->validate(User::RULES);

            $user = new User();
            $user->fill($request->all());

            $this->userService->save($user);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Cadastro efetuado com sucesso, você reberá um email para confirmação'
                ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function activate(Request $request)
    {
        $token = $request->token;

        $this->userService->activateUser($token);

        return view('activation');
    }

    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => 'required',
        ];

        $request->validate($rules);

        $email = $request->email;
        
        try {
            $this->userService->sendResetLinkEmail($email);
        } catch (\Exception $e) {}

        return response()->json(
            [
                'status' => true,
                'message' => 'Se houver uma conta associada a este usuário um email com instruções será enviado.'
            ], 200);
    }

    public function showProfile()
    {
        $user = Auth::user();
        $user['photo'] = route('profilePic');

        return response()->json(['success' => true, 'data' => Auth::user()], 200);
    }

    public function updateProfile(Request $request)
    {
        try{
            $user = Auth::user();

            $rules = User::RULES;

            unset($rules['password']);

            if ($request->get('email') == $user->email) {
                unset($rules['email']);
            }

            $request->validate($rules);

               //se não estiver vazio, é troca de senha também
            if (!empty($request->get('old_password')) && !empty($request->get('password'))) {
                $rules['password'] = ['required','min:8','max:20'];

                if (!\Hash::check($request->get('old_password'), $user->password)) {
                    throw new \Exception('Senha antiga inválida');
                }

                $user->password = \Hash::make($request->get('password'));
            }


            $user->fill($request->except('password'));

            $this->userService->save($user);

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);

        }
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json(['success' => true], 200);
    }

    public function updatePicture(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
        ];

        $request->validate($rules);

        if (!empty($user->profile_pic)) {
            \File::delete(storage_path('/app/'.$user->profile_pic));
        }

        $photo = $request->file('file');

        $path  = $photo->store('profile/pics');

        $destinationPath = storage_path('/app/profile/pics/thumbnail/'.$photo->hashName());

        $this->userService->generateThumbnail(storage_path('/app/'.$path), $destinationPath , 150, 150);

        $user->profile_pic = 'profile/pics/thumbnail/'.$photo->hashName();

        \File::delete(storage_path('/app/'.$path));

        $this->userService->save($user);

        return response()->json(['success' => true, 'message' => 'Imagem alterada com sucesso'], 200);
    }

    public function getPicture()
    {
        $user = Auth::user();
        $path = storage_path('app/'.$user->profile_pic);
        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }

}
