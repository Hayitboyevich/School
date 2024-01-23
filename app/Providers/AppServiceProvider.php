<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Quiz;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        TextInput::macro('revealOnHover', function () {
            return $this
                ->type('password')
                ->extraInputAttributes([
                    'x-on:mouseover' => "\$el.type = 'text'",
                    'x-on:mouseout' => "\$el.type = 'password'",
                ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Panel $panel): void
    {
        Relation::morphMap([
            'NEW_BOOK' => Book::class,
            'BOOK' => Book::class,
            'BOOK_CHAPTER' => BookChapter::class,
            'QUIZ' => Quiz::class,
        ]);

        FilamentAsset::register([
            Css::make('filament-customizations', __DIR__ . '/../../resources/css/filament-customizations.css')
        ]);
    }
}
