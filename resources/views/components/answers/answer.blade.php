<table class="w-full">
    <thead class="bg-gray-100">
    <tr>
        <th class="border font-semibold p-4 text-left">№</th>
        <th class="border font-semibold p-4 text-left">Правильный ответ</th>
        <th class="border font-semibold p-4 text-left">Ваш ответ</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($attempt->attempt_questions as $key => $question)
        <tr>
            <td class="border p-4">{{ $key + 1 }}</td>
            <td class="border p-4">{!! $question->attempt_answers->firstWhere('is_correct', true)->content !!}</td>
            <td class="border p-4
                @if ($question->attempt_answers->filter(fn($answer) => $answer->is_correct)->every(fn($answer) => $answer->is_selected))
                    text-emerald-500
                @else
                    text-red-500
                @endif
            ">
                {!! $question->attempt_answers->firstWhere('is_selected', true) ? $question->attempt_answers->firstWhere('is_selected', true)->content : '' !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
