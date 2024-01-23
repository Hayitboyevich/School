@switch($chapter->status)
    @case(null)
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg w-full"
           href="{{ route('book-chapter.change-status', ['id' => $chapter->id, 'status' => \App\Models\Enums\BookUserStatus::STARTED]) }}">
            Начать
        </a>
        @break
    @case(\App\Models\Enums\BookUserStatus::STARTED->value)
        <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 w-full"
           href="{{ route('book-chapter.change-status', ['id' => $chapter->id, 'status' => \App\Models\Enums\BookUserStatus::PAUSED]) }}">
            Остановить
        </a>
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg w-full"
           href="{{ route('book-chapter.change-status', ['id' => $chapter->id, 'status' => \App\Models\Enums\BookUserStatus::FINISHED]) }}">
            Закончить
        </a>
        @break
    @case(\App\Models\Enums\BookUserStatus::PAUSED->value)
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg w-full"
           href="{{ route('book-chapter.change-status', ['id' => $chapter->id, 'status' => \App\Models\Enums\BookUserStatus::STARTED]) }}">
            Продолжить
        </a>
        <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg w-full"
           href="{{ route('book-chapter.change-status', ['id' => $chapter->id, 'status' => \App\Models\Enums\BookUserStatus::FINISHED]) }}">
            Закончить
        </a>
        @break
@endswitch
