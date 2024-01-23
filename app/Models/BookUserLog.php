<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookUserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'status',
        'date'
    ];
}

