<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;


class MyBook extends Pivot
{
    use HasFactory;

    protected $table = 'my_books';

    protected $fillable = ['current_page','status','notes','user_id','book_id','file'];

    static $rules = [
        'name' => 'required|max:200',
        'author' => 'required|max:200',
        'pages' => 'required|integer',
        'image' => 'required',
    ];

    public function atualizaPagina($page)
    {
       if ($page > $this->book->pages) {
            throw new \Exception('Não pode fazer esta operação');
       }
       
        if ($page < 0) {
            throw new \Exception('Página inválida');
        }

   
       if ($page == $this->book->pages) {
           $this->status = 'finished';
       } else {
           $this->status = 'in_progress';
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
