<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

Route::get('/', function () {
    return "api";
});

Route::get('/teste', function (Request $request) {
    // dd(Crypt::decryptString('eyJpdiI6IlBEQWdOVk80WEw5eUtMVnlLdTVmcUE9PSIsInZhbHVlIjoiK21ZNmZYQWJoSlVEKzF4NnJpWFJYNVpndHQzVkJneEcvc2ZCOUdlSjQxUmpwSi9YbWtwY1ZmZWVUd3hZTDZqTXFiMzY5TEN4K0pMQmluNGFSS2svc2tsZWxJS0RuWXZuQzRRcVQ2VzhiSGdhT2dWUHV6NGtiWDFjZm1MZkhaY2oiLCJtYWMiOiIyNTM4NjViODYxZWZmYjNhNGQyZDUzY2UwNzE4ZGFhY2Y2ZTJmYzkyMjVhMTEzNmVjZDIyYTU3Mzg1MWUzMGI5IiwidGFnIjoiIn0%3D'));
    // $credentials = [
    //     'email' => 'teste@example.com',
    //     'password' => '22011991'
    // ];

    // if (Auth::attempt($credentials)) {
    //     $request->session()->regenerate();

    // }
});
