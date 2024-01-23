<x-app-layout>
    <h2 class="text-4xl">Тесты</h2>
    <div class="grid grid-cols-3 gap-4">
        @foreach ($quizzes as $quiz)
            <x-quizzes.quiz :quiz="$quiz"/>
        @endforeach
    </div>
    <x-pagination :items="$quizzes"/>
</x-app-layout>
