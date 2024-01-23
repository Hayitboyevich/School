@if($chapters->count() > 0)
    <h3 class="font-semibold text-lg">{{__('models/book_chapter.plural')}}</h3>
    @foreach ($chapters as $chapter)
        <div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto p-4">
            <x-book-chapters.chapter :chapter="$chapter"/>
        </div>
    @endforeach
@endif
