<x-app-layout>
    <h3 class="font-semibold text-lg">{!! $attempt->attempt_question->content !!}</h3>
    <form action="{{ route('quizzes.attempt', $attempt->quiz_id) }}" method="POST">
        @csrf
        <x-questions.answer :answers="$attempt->attempt_question->attempt_answers"/>
        <x-questions.footer :current="$attempt->attempt_question_order" :count="$attempt->attempt_questions->count()"/>
    </form>
</x-app-layout>
