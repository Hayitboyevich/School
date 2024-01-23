<x-app-layout>
    <div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto ">
        <h2 class="font-semibold text-3xl">{{ $quiz->name }}</h2>
        <div>{!! $quiz->description !!}</div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-gray-400">Автор теста</div>
                <div>{{ $quiz->authors->count() > 0 ? $quiz->authors->pluck('name')->join(', ') : '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">Тип теста</div>
                <div>{{ $quiz->test_type->label ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">Время на тест</div>
                <div>{{ human_duration($quiz->duration) ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">Вопросы</div>
                <div>{{ $quiz->questions->count() }}</div>
            </div>
            <div>
                <div class="text-gray-400">Предмет</div>
                <div>
                    @if ($quiz->subjects->count() > 0)
                        {{ $quiz->subjects->pluck('name')->join(', ') }}
                    @endif
                    @if ($quiz->subjects->count() > 0 && $quiz->books->count() > 0)
                        ,
                    @endif
                    @if ($quiz->books->count() > 0)
                        {{ $quiz->books->pluck('name')->join(', ') }}
                    @endif
                </div>
            </div>
            <div>
                <div class="text-gray-400">Классы</div>
                <div>{{ $quiz->groups->count() > 0 ? $quiz->groups->pluck('name')->join(', ') : '—' }}</div>
            </div>
        </div>
        @if($attempts->count() == 0)
            <form action="{{ route('quizzes.attempt', $quiz->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg w-full">
                    Начать тест
                </button>
            </form>
        @endif
    </div>
    </div>
    @if($attempts->count() > 0)
        <div class="mx-auto my-10 space-y-5 px-10 max-w-7xl ">
            <div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto w-full ">
{{--                <x-answers.header-logo/>--}}
                <x-answers.new-about :attempt="$attempt"/>
{{--                <h3 class="font-semibold text-lg">Подробный отчет по тесту</h3>--}}
{{--                <x-answers.answer :attempt="$attempt"/>--}}
{{--                <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined"--}}
{{--                   href="{{ route('quizzes.index') }}">--}}
{{--                    Вернуться к другим тестам--}}
{{--                </a>--}}
            </div>
    @endif
</x-app-layout>
