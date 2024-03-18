<?php

namespace Modules\Perpustakaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $guarded = '';

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'book_id', 'id');
    }

    public function bookAuthors()
    {
        return $this->hasMany(BookAuthor::class, 'book_id', 'id');
    }

    public function bookAuthor()
    {
        return $this->belongsTo(BookAuthor::class, 'id', 'book_id');
    }
}
