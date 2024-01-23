<div class="fixed p-5 right-0 bottom-0 left-0 bg-white flex justify-between items-center border-t">
    @if ($current > 1)
        <button type="submit" name="direction" value="prev"
                class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800">
            Назад
        </button>
    @else
        <div></div>
    @endif
    <div class="text-gray-500">
        <span class="font-semibold text-black">Вопрос {{ $current }}</span> <span>/ {{ $count }}</span>
    </div>
    @if ($current != $count)
        <button type="submit" name="direction" value="next"
                class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg">
            Вперед
        </button>
    @else
        <button type="submit" name="direction" value="finish"
                class="flex justify-center items-center gap-2 bg-blue-800 text-white py-2 px-3 rounded-lg">
            Закончить
        </button>
    @endif
</div>
