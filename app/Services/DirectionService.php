<?php

namespace App\Services;

use App\Models\Direction;
use Illuminate\Support\Facades\Http;

class DirectionService
{
    protected Direction $directions;

    public function __construct(Direction $directions)
    {
        $this->directions = $directions;
    }

    public function sync_sehriyo()
    {
        $url = env('SEHRIYO_LMS_HOST') . '/directions';
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->withBasicAuth(env('SEHRIYO_LMS_USER'), env('SEHRIYO_LMS_PASSWORD'))
            ->get($url);
        $directions_data = $response['data'];

        foreach ($directions_data as $direction_data) {
            $direction_data['external_id'] = $direction_data['id'];
            $direction_data['label'] = $direction_data['name'];
            unset($direction_data['id']);
            unset($direction_data['name']);

            $direction = $this->directions->where('external_id', $direction_data['external_id'])->first();
            if ($direction == null) {
                $this->directions->create($direction_data);
            } else {
                $direction->update($direction_data);
            }
        }
    }
}
