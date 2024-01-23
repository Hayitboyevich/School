<div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto p-4">
    <div class="flex items-start gap-4 justify-between">
        <div class="flex gap-4">
            <x-book-chapters.badge :status="$book->status"/>
            <div>
                <div>«{{ $book->name }}»</div>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <div class="text-gray-400">{{__('models/book_chapter.prop.start_date')}}</div>
                <div>{{ $book->start_date ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">{{__('models/book_chapter.prop.end_date')}}</div>
                <div>{{ $book->end_date ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">{{__('models/book_chapter.prop.time_spent')}}</div>
                <div>{{ human_diff($book->started, $book->finished) ?? '—' }}</div>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <div class="text-gray-400">{{__('frontend.action.start')}}</div>
                <div>{{ $book->started ?? '—' }}</div>
            </div>
            <div>
                <div class="text-gray-400">{{__('frontend.action.ending')}}</div>
                <div>{{ $book->finished ?? '—' }}</div>
            </div>
        </div>
        <div class="flex flex-col gap-4">
            <div>
                <div class="text-gray-400">{{__('models/book_chapter.prop.score')}}</div>
                <div>—</div>
            </div>
            <div>
                <div class="text-gray-400">{{__('frontend.action.development')}}</div>
                <div>—</div>
            </div>
        </div>
        <div class="w-56 space-y-2">
            <x-books.actions :book="$book"/>
        </div>
    </div>
</div>
