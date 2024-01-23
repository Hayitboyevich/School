<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class  AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $year = Carbon::now()->year;

            if ($i < 2) {
                $year = Carbon::now()->subYears(2 - $i)->year;
            } elseif ($i === 3) {
                $year = Carbon::now()->addYear()->year;
            }

            AcademicYear::create([
                'start' => Carbon::createFromDate($year, 9, 4)->format('Y-m-d'),
                'end' => Carbon::createFromDate($year + 1, 5, 25)->format('Y-m-d'),
                'quarter1_start_date' => Carbon::createFromDate($year, 9, 4)->format('Y-m-d'),
                'quarter1_end_date' => Carbon::createFromDate($year, 11, 3)->format('Y-m-d'),
                'quarter2_start_date' => Carbon::createFromDate($year, 11, 10)->format('Y-m-d'),
                'quarter2_end_date' => Carbon::createFromDate($year, 12, 27)->format('Y-m-d'),
                'quarter3_start_date' => Carbon::createFromDate($year + 1, 1, 12)->format('Y-m-d'),
                'quarter3_end_date' => Carbon::createFromDate($year + 1, 3, 20)->format('Y-m-d'),
                'quarter4_start_date' => Carbon::createFromDate($year + 1, 3, 28)->format('Y-m-d'),
                'quarter4_end_date' => Carbon::createFromDate($year + 1, 5, 25)->format('Y-m-d')
            ]);
        }
    }
}
