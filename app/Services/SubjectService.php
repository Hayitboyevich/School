<?php

namespace App\Services;

use App\Models\Direction;
use App\Models\Subject;
use Illuminate\Support\Facades\Http;

class SubjectService
{
    private Subject $subjects;

    private Direction $directions;

    public function __construct(Subject $subjects, Direction $directions)
    {
        $this->subjects = $subjects;
        $this->directions = $directions;
    }

    public function sync_sehriyo()
    {
        $url = env('SEHRIYO_LMS_HOST') . '/subjects';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->get($url);
        $subjects_data = $response['data'];

        foreach ($subjects_data as $subject_data) {
            $subject_data['external_id'] = $subject_data['id'];
            unset($subject_data['id']);
            $direction = $this->directions->where('external_id', $subject_data['direction_id'])->first();
            $subject['direction_id'] = $direction->id ?? null;

            $subject = $this->subjects->where('external_id', $subject_data['external_id'])->first();
            if ($subject == null) {
                $this->subjects->create($subject_data);
            } else {
                $subject->update($subject_data);
            }
        }
    }
}
