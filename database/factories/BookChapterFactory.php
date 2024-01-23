<?php

namespace Database\Factories;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookChapter>
 */
class BookChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'start_date' => Carbon::createFromDate(Carbon::now()->year, rand(1, 12), rand(1, 28))->format('Y-m-d'),
            'end_date' => Carbon::createFromDate(Carbon::now()->addYear()->year, rand(1, 12), rand(1, 28))->format('Y-m-d'),
            'score' => rand(0, 100)
        ];
    }
}
