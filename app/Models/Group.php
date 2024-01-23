<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'group_level',
        'group_letter',
        'academic_year_ids',
    ];

    protected $casts = [
        'academic_year_ids' => 'array'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getNameAttribute()
    {
        return $this->group_level . ' ' . $this->group_letter;
    }

    public function getAcademicYearsAttribute()
    {
        return AcademicYear::whereIn('external_id', $this->academic_year_ids)->get()->append('period');
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class)->withPivot('start_date', 'end_date');
    }

    public function book_chapters()
    {
        return $this->belongsToMany(BookChapter::class);
    }
}
