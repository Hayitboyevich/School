<x-app-layout>
    @if($attempts->count() > 0)
        <div class="mx-auto my-10 space-y-5 px-10 max-w-7xl ">
            <div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto w-full ">
                @if(($attempt->correct / $attempt->all * 100) >= 56 )
                  <x-answers.header-logo/>
                @else
                    <x-answers.header-failur-logo/>
                @endif
                <x-answers.new-about :attempt="$attempt"/>
                <h3 class="font-semibold text-lg">Подробный отчет по тесту</h3>
                <x-answers.answer :attempt="$attempt"/>
                <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 undefined"
                   href="{{ route('quizzes.index') }}">
                    Вернуться к другим тестам
                </a>
            </div>
        </div>
    @endif
</x-app-layout>
