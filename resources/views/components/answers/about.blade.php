<div class="grid grid-cols-3 gap-4">
    <div>
        <div class="text-gray-400">Правильные ответы</div>
        <div>{{ $attempt->correct }}</div>
    </div>
    <div>
        <div class="text-gray-400">Неправильные ответы</div>
        <div>{{ $attempt->incorrect }}</div>
    </div>
    <div>
        <div class="text-gray-400">Всего вопросов</div>
        <div>{{ $attempt->all }}</div>
    </div>
    <div>
        <div class="text-gray-400">Оценка за тест</div>
        <div>0</div>
    </div>
    <div>
        <div class="text-gray-400">Затраченное время</div>
        <div>{{ \Carbon\Carbon::parse($attempt->end_date)->diff(\Carbon\Carbon::parse($attempt->start_date))->format('%H:%I:%S') }}</div>
    </div>
    <div>
        <div class="text-gray-400">Время на тест</div>
        <div>30 минут</div>
    </div>
    <div>
        <div class="text-gray-400">Место в классе</div>
        <div>19</div>
    </div>
    <div>
        <div class="text-gray-400">Место по всей школе</div>
        <div>187</div>
    </div>
    <div>
        <div class="text-gray-400">Набранные баллы из 289</div>
        <div>135</div>
    </div>
    <div>
        <div class="text-gray-400">Процент освоения из 100%</div>
        <div>
            @if($attempt->all > 0)
                {{ number_format($attempt->correct / $attempt->all * 100) }}%
            @else
                -
            @endif
        </div>
    </div>
</div>
