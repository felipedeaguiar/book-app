<?php

namespace App\Http\Controllers;

use App\Services\GoogleService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $googleService;

    public function __construct(
        GoogleService $googleService
    ){
        $this->googleService = $googleService;
    }

    public function generos(Request $request)
    {
        $user = \Auth::user();

        $books = $user->books;
        $response = [];

        foreach ($books as $book) {
            $result = $this->googleService->getVolume($book->google_id);
            if (!empty($result->volumeInfo->categories)) {
                if (!array_key_exists($result->volumeInfo->categories[0], $response)) {
                    $response[$result->volumeInfo->categories[0]] = 1;
                } else {
                    $response[$result->volumeInfo->categories[0]] += 1;
                }
            }
        }

        return response()->json(['status' => true, 'data' => $response], 200);
    }
}
