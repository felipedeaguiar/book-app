<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['name','author','pages','image'];

    protected $hidden = ['created_at', 'updated_at'];

    static $rules = [
        'name' => 'required|max:200',
        'author' => 'required|max:200',
        'pages' => 'required|integer',
        'image' => 'required'
    ];

    public function books()
    {
        return $this->belongsToMany(User::class,'my_books')
            ->using(MyBook::class)
            ->withPivot(['id','current_page','status']);
    }
}
