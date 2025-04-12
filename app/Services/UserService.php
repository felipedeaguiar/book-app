<?php

namespace App\Services;

use App\Mail\ResetPassword;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Psy\Util\Str;

class UserService
{
    public function save(User $user): User {

        if (!$user->id) {
            $user->email_verification_token = \Illuminate\Support\Str::random(60);
        }

        $user->save();

        $this->notificaNovoUsuario($user);

        return $user;
    }

    public function get(array $filters)
    {
        $users = User::query();

        if (array_key_exists('email_verified_at', $filters)) {
            $users->where('email_verified_at', $filters['email_verified_at']);
        }

        if (array_key_exists('email_verification_token', $filters)) {
            $users->where('email_verification_token', $filters['email_verification_token']);
        }


        return $users->get();
    }

    public function notificaNovoUsuario(User $user)
    {
        if (!$user->id) {
            Mail::to($user->email)->send(new WelcomeUserMail($user));
        }

        return true;
    }

    public function activateUser($token): bool
    {
        $filter = [
            'email_verification_token' => $token,
            'email_verified_at' => null
        ];

        $user = $this->get($filter)->first();

        if (empty($user)) {
            return false;
        }

        $user->email_verified_at = now();
        $user->save();

        return true;
    }

    public function sendResetLinkEmail($email): bool
    {
        $user = User::where('email', $email)->first();

        if (empty($user)) {
            throw new \Exception('User not found');
        }

        $token = \Illuminate\Support\Str::random(60);
        $email = $user->email;


        \DB::table('password_reset_tokens')->updateOrInsert([
            'email' => $email,
        ],[
            'token' => hash('sha256', $token),  // Armazenar o hash do token
            'created_at' => now(),
        ]
        );

        $resetLink = url("/reset-password/".$token);

        $params = [
            'reset_link' => $resetLink,
            'email'      => $email
        ];

       Mail::to($email)->send(new ResetPassword($params));

       return true;
    }

}
