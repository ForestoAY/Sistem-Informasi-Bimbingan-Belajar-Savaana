<?php

namespace Modules\Perpustakaan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;

    protected $guarded = '';

    public function authorBooks()
    {
        return $this->hasMany(BookAuthor::class, 'author_id', 'id');
    }
}
