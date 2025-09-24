<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = User::all();

        return response()->json($contacts);
    }
}
