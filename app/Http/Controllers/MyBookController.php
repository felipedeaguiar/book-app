<?php

namespace App\Http\Controllers;

use App\Http\GoogleService;
use App\Models\Book;
use App\Models\MyBook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBookController extends Controller
{
    //
    protected $googleService;

    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function index(Request $request)
    {
        $query = Auth::user()->books();

        if ($request->has('search') && !empty($request->search) && $request->search != 'undefined') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $result = $query->orderBy('created_at', 'DESC')->get();

        return response()->json($result);
    }

    public function detail($id)
    {
        $myBook = Auth::user()->books()->where('book_id', $id)->first();

        if (empty($myBook)) {
            return response()->json(['success' => false, 'message' => 'Book not found'], 404);
        }


        return response()->json(['success' => true, 'data' => $myBook]);
    }

    public function store(Request $request)
    {
        $rules = [
            'book_id' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $book = Book::where('google_id', $request->book_id)->first();

        if (empty($book)) {
            $result = $this->googleService->getVolume($request->book_id);

            if (property_exists($result, 'error')) {
                throw new \Exception('Invalid book');
            }

            $book         = new Book();
            $book->name   = $result->volumeInfo->title;
            $book->author = $result->volumeInfo->authors ? $result->volumeInfo->authors[0] : '';
            $book->pages  = $result->volumeInfo->pageCount;
            $book->image  = $result->volumeInfo->imageLinks->smallThumbnail;
            $book->google_id = $result->id;

            $book->save();
        }

        $myBook = Auth::user()->books()->attach($book->id,[
            'current_page' => 0,
            'status' => 'in_progress',
            'notes' => null,
        ]);

        return response()->json(['success' => true, 'data'=> $myBook],201);
    }

    public function changePage(Request $request, $id)
	{
        try {
            $rules = [
                'page' => 'required|integer',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            $userBook = Auth::user()->books()->where('book_id', $id)->first();

            $userBook->pivot->atualizaPagina($request->page);

            return response()->json(['success' => true]);

        }catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => !is_string($exception->getMessage()) ? json_decode($exception->getMessage()) : $exception->getMessage()
            ]);
        }
	}

	public function destroy($id)
    {
        try {
            if (Auth::user()->books()->where('book_id', $id)->first()) {
                Auth::user()->books()->detach($id);
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'Não foi encontrado']);
        }
    }

    public function upload()
    {
        $rules = [
            'file' => 'required|mimes:pdf|max:8192',
        ];

        $validator = \Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::user()->id;

        $file = request()->file('file');
        $path = $file->store('user/'.$userId.'/books');

        $myBook       = MyBook::where('user_id', $userId)->where('book_id', request()->id)->first();
        $myBook->file = $path;
        $myBook->save();

        return response()->json(['success' => true, 'path' => $path], 201);
    }

    public function download()
    {
        $userId = Auth::user()->id;

        $myBook = MyBook::where('user_id', $userId)->where('book_id', request()->id)->first();

        if (empty($myBook) || empty($myBook->file)) {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }

        $path     = storage_path('app/'.$myBook->file);

        if (!file_exists($path)) {
            return response()->json(['success' => false, 'message' => 'File not found'], 404);
        }

        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }
}
