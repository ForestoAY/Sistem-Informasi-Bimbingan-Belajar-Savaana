<?php

namespace Modules\Perpustakaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookAuthor extends Model
{
    use HasFactory;

    protected $guarded = '';

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
