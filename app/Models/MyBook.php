<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


class MyBook extends Pivot
{
    use HasFactory;

    protected $fillable = ['current_page','status','notes','user_id','book_id'];

    static $rules = [
        'name' => 'required|max:200',
        'author' => 'required|max:200',
        'pages' => 'required|integer',
        'image' => 'required'
    ];

    public function atualizaPagina($page)
    {
       if ($page > $this->book->pages) {
            throw new \Exception('Não pode fazer esta operação');
       }

       if ($this->status == 'finished') {
            throw new \Exception('Não é possível mudar um livro finalizado');
       }

       if ($page == $this->book->pages) {
           $this->status = 'finished';
       }

       $this->current_page = $page;
       $this->save();
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
