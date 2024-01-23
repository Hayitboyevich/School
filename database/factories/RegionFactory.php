<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->region(),
            'country_id' => Country::select('id')->inRandomOrder()->first()
        ];
    }
}
