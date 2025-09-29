<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Services\GoogleService as ServicesGoogleService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $googleService;

    public function __construct(ServicesGoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->all());

        return response()->json($book);
    }

    public function search(Request $request)
    {
        $result = [];

        if ($request->has('search') && !empty($request->search)) {
            $books = $this->googleService->getVolumes($request->search);

            if (!empty($books)) {
                foreach ($books->items as $key => $item) {
                    $result[$key]['id']   = $item->id;
                    $result[$key]['name'] = $item->volumeInfo->title;
                }
            }
        }

        return $result;
    }
}
