<?php

namespace App\Services;

use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AcademicYearService
{
    protected AcademicYear $academic_years;

    public function __construct(AcademicYear $academic_years)
    {
        $this->academic_years = $academic_years;
    }

    public function sync_sehriyo()
    {
        $url = env('SEHRIYO_LMS_HOST') . '/academic-years';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->get($url);
        $academic_years_data = $response['data'];

        foreach ($academic_years_data as $academic_year_data) {
            $academic_year_data['external_id'] = $academic_year_data['id'];
            unset($academic_year_data['id']);

            $academic_year = $this->academic_years->where('external_id', $academic_year_data['external_id'])->first();
            if ($academic_year == null) {
                $this->academic_years->create($academic_year_data);
            } else {
                $academic_year->update($academic_year_data);
            }
        }
    }

    public function getDefault()
    {
        $now = Carbon::now()->toDateString();
        $academic_year = $this->academic_years
            ->where('start', '<=', $now)
            ->where('end', '>=', $now)
            ->whereNotNull('external_id')
            ->first();
        if ($academic_year === null) {
            return $this->academic_years
                ->orderBy('id', 'desc')
                ->first();
        }
        return $academic_year;
    }
}
