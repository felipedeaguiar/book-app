<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyBook extends Model
{
    use HasFactory;

    protected $fillable = ['current_page','status','notes','user_id','book_id'];

    static $rules = [
        'name' => 'required|max:200',
        'author' => 'required|max:200',
        'pages' => 'required|integer',
        'image' => 'required'
    ];

}
