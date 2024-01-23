<?php

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum FeedbackType: string implements HasLabel
{
    case NEW_BOOK = 'NEW_BOOK';
    case BOOK = 'BOOK';
    case BOOK_CHAPTER = 'BOOK_CHAPTER';
    case QUIZ = 'QUIZ';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NEW_BOOK => 'Новая книга',
            self::BOOK => 'Книга',
            self::BOOK_CHAPTER => 'Раздел книги',
            self::QUIZ => 'Тест',
        };
    }
}
