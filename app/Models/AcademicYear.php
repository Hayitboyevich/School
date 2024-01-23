<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'start',
        'end',
        'quarter1_start_date',
        'quarter1_end_date',
        'quarter2_start_date',
        'quarter2_end_date',
        'quarter3_start_date',
        'quarter3_end_date',
        'quarter4_start_date',
        'quarter4_end_date',
        'external_id'
    ];

    public function getPeriodAttribute()
    {
        if ($this['start'] && $this['end']) {
            return \Carbon\Carbon::parse($this['start'])->year . " – " . \Carbon\Carbon::parse($this['end'])->year;
        }
        return '';
    }

    public function getFullPeriodAttribute()
    {
        return $this->getFullPeriodOf('start', 'end');
    }

    public function getQuarterPeriod($quarter)
    {
        return $this->getFullPeriodOf('quarter' . $quarter . '_start_date', 'quarter' . $quarter . '_end_date');
    }

    private function getFullPeriodOf($start_column, $end_column)
    {
        if ($this[$start_column] && $this[$end_column]) {
            return \Carbon\Carbon::parse($this[$start_column])->format('d.m.Y') . " – " . \Carbon\Carbon::parse($this[$end_column])->format('d.m.Y');
        }
        return '';
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
