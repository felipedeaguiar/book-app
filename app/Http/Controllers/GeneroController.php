<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{

    public function __construct(){}

    public function index(Request $request)
    {
       $generos = Genero::all();

        return response()->json(['status' => true, 'data' => $generos], 200);
    }
}
