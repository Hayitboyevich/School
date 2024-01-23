<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookAuthor;
use App\Models\BookChapter;
use App\Models\Genre;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
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
            'cover' => null,
            'description' => fake()->text(),
            'page_count' => rand(3, 300),
            'file_url' => null,
            'reference_link' => null,
//            'group_level' => $this->getRandomArray(5, 11),
            'start_date' => Carbon::createFromDate(Carbon::now()->year, rand(1, 12), rand(1, 28))->format('Y-m-d'),
            'end_date' => Carbon::createFromDate(Carbon::now()->addYear()->year, rand(1, 12), rand(1, 28))->format('Y-m-d'),
            'score' => rand(0, 100),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Book $book) {
            $this->saveChapter($book);
            $book->book_authors()->attach(
                BookAuthor::inRandomOrder()->limit(rand(1, 3))->pluck('id')->toArray()
            );
            $book->genres()->attach(
                Genre::inRandomOrder()->limit(rand(1, 5))->pluck('id')->toArray()
            );
            $book->academic_years()->attach(
                AcademicYear::inRandomOrder()->limit(rand(1, 2))->pluck('id')->toArray()
            );
        });
    }

    private function getRandomArray($qty, $num)
    {
        $quantity = rand(1, $qty);
        $numbers = range(1, $num);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity) ?? null;
    }

    private function saveChapter($book)
    {
        $quantity = rand(1, 50);
        $endPage = 0;
        for ($i = 1; $i <= $quantity; $i++) {
            $startPage = $endPage + 1;
            $endPage += floor($book->page_count / $quantity);
            BookChapter::factory()->create([
                'book_id' => $book->id,
                'page_start' => $i == 1 ? 3 : $startPage,
                'page_end' => $endPage,
            ]);
        }
    }
}
