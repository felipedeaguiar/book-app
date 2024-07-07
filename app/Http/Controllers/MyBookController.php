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
        $result = Auth::user()->books;
        
        return response()->json($result);
    }

    public function store(Request $request)
    {

        $rules = [
            'book_id' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);


        $book = Book::where('google_id', $request->book_id)->first();

        if (empty($book)) {
            $result = $this->googleService->getVolume($request->book_id);
            $book         = new Book();
            $book->name   = $result->volumeInfo->title;
            $book->author = $result->volumeInfo->authors[0];
            $book->pages  = $result->volumeInfo->pageCount;
            $book->image  = $result->volumeInfo->imageLinks->smallThumbnail;
            $book->google_id = $result->id;

            $book->save();
        }

        $myBook = MyBook::create([
            'user_id' => 1,
            'book_id' => $book->id,
            'current_page' => 0, // Por padrão, inicia na primeira página
            'status' => 'in_progress',
            'notes' => null, // Notas iniciais, se necessário
        ]);

        return response()->json(['success' => true, 'data'=> $myBook],201);
    }

    public function changePage(Request $request, $id)
	{
		$rules = [
			'page' => 'required',
		];

		$validator = \Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			 return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			], 422);
		}

		$myBook = MyBook::findOrFail($id);

		if ($request->page > Book::find($myBook->book_id)->pages) {
			throw new \Exception('Não pode');
		}

		$myBook->current_page = $request->page;
		$myBook->save();

		return $myBook;
	}
}
