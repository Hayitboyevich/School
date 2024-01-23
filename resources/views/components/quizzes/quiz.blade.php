<div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto w-full !p-5">
    @if ($quiz->subjects->isNotEmpty())
        <div>
            @foreach ($quiz->subjects as $subject)
                <div class="inline-flex items-center rounded-lg bg-gray-100 px-2 py-1 text-gray-500">
                    {{ $subject->name }}
                </div>
            @endforeach
        </div>
    @endif
    <h3 class="font-semibold">{{ $quiz->name }}</h3>
    <div>
        @if($quiz->books->isNotEmpty())
            <div class="text-gray-400">Название книги</div>
            <div>{{ $quiz->books->pluck('name')->join(", ") }}</div>
        @endif
    </div>
    <div>
        @if($quiz->book_chapters->isNotEmpty())
            <div class="text-gray-400">Название раздела</div>
            <div>{{ $quiz->book_chapters->pluck('name')->join(", ") }}</div>
        @endif
    </div>
    <div class="flex gap-4">
        <div>
            <div class="text-gray-400">Процент</div>
            <div>100%</div>
        </div>
        <div>
            <div class="text-gray-400">Баллы</div>
            <div>271</div>
        </div>
        <div>
            <div class="text-gray-400">Оценка</div>
            <div>5</div>
        </div>
    </div>
    <div class="bg-yellow-100 rounded-full overflow-hidden shadow-sm">
        <div class="bg-yellow-500 h-2 w-10" style="width: 13%"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-gray-400">Сдавать до</div>
            <div>11 декабря 2023</div>
        </div>
        <div>
            <div class="text-gray-400">Дата сдачи</div>
            <div>11 декабря 2023</div>
        </div>
    </div>
    <a class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg undefined"
       href="{{ route('quizzes.show', $quiz->id) }}">
        Подробно
    </a>
{{--    <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined" href="{{route('quizzes.result', $quiz->id)}}">--}}
{{--        Посмотреть результаты--}}
{{--    </a>--}}
</div>
