<x-app-layout>
    <h2 class="text-4xl">Тесты</h2>
    <div class="grid grid-cols-3 gap-4">
        @foreach ($quizzes as $quiz)
            <x-quizzes.quiz :quiz="$quiz"/>
        @endforeach
    </div>
    <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined"
       href="{{ route('quizzes.index') }}">
        Все тесты
    </a>
    <h2 class="text-4xl">@lang('models/book.plural')</h2>
    <x-books.filter/>
    <div class="grid grid-cols-4 gap-5">
        @foreach ($books as $book)
            <x-books.item :book="$book"/>
        @endforeach
    </div>
    <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined"
       href="{{ route('books.index') }}">
        Все книги
    </a>
</x-app-layout>
