<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ' ' . $this->middle_name;
    }

}
