<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books/search', [\App\Http\Controllers\BookController::class,'search']);
    Route::post('/books', [\App\Http\Controllers\BookController::class,'store']);
    Route::post('/my-books', [\App\Http\Controllers\MyBookController::class,'store']);
    Route::get('/my-books', [\App\Http\Controllers\MyBookController::class,'index']);
    Route::put('/my-books/{id}/change-page', [\App\Http\Controllers\MyBookController::class,'changePage']);
    Route::delete('/my-books/{id}', [\App\Http\Controllers\MyBookController::class,'destroy']);
    Route::get('/user/profile', [\App\Http\Controllers\AuthController::class,'showProfile']);
    Route::put('/user/profile', [\App\Http\Controllers\AuthController::class,'updateProfile']);
    Route::post('/user/profile/picture', [\App\Http\Controllers\AuthController::class,'updatePicture']);
    Route::get('/user/profile/picture', [\App\Http\Controllers\AuthController::class,'getPicture'])->name('profilePic');

});

Route::post('/login', [\App\Http\Controllers\AuthController::class,'login']);
Route::post('/register', [\App\Http\Controllers\AuthController::class,'register']);
Route::get('/activation/{token}', [\App\Http\Controllers\AuthController::class,'activate'])->name('activation');
Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class,'forgotPassword'])->name('forgotPassword');
