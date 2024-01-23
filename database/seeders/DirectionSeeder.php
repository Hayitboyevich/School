<?php

namespace Database\Seeders;

use App\Models\Direction;
use Illuminate\Database\Seeder;

class DirectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            'Точные науки',
            'Гуманитарные науки'
        ];
        foreach ($records as $record) {
            Direction::create(['label' => $record]);
        }
    }
}
