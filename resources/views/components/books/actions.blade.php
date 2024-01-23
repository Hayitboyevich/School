@switch($book->status)
    @case(null)
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
           href="{{ route('books.change-status', ['id' => $book->id, 'status' => \App\Models\Enums\BookUserStatus::STARTED]) }}">
            Начать читать
        </a>
        @break
    @case(\App\Models\Enums\BookUserStatus::STARTED->value)
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
           href="{{ route('books.change-status', ['id' => $book->id, 'status' => \App\Models\Enums\BookUserStatus::PAUSED]) }}">
            Приостановить
        </a>
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
           href="{{ route('books.change-status', ['id' => $book->id, 'status' => \App\Models\Enums\BookUserStatus::FINISHED]) }}">
            Закончить
        </a>
        @break
    @case(\App\Models\Enums\BookUserStatus::PAUSED->value)
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
           href="{{ route('books.change-status', ['id' => $book->id, 'status' => \App\Models\Enums\BookUserStatus::STARTED]) }}">
            Продолжить
        </a>
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
           href="{{ route('books.change-status', ['id' => $book->id, 'status' => \App\Models\Enums\BookUserStatus::FINISHED]) }}">
            Закончить
        </a>
        @break
@endswitch
