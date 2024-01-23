<div class="flex items-start gap-4 justify-between">
    <div class="flex gap-4">
        <x-book-chapters.badge :status="$chapter->status"/>
        <div>
            <div class="text-gray-400">{{__('models/book_chapter.singular')}}</div>
            <div>«{{ $chapter->name }}»</div>
        </div>
    </div>
    <div class="flex flex-col gap-4">
        <div>
            <div class="text-gray-400">{{__('models/group.prop.start_date')}}</div>
            <div>{{ human_date($chapter->start_date) ?? '—' }}</div>
        </div>
        <div>
            <div class="text-gray-400">{{__('models/group.prop.end_date')}}</div>
            <div>{{ human_date($chapter->end_date) ?? '—' }}</div>
        </div>
        <div>
            <div class="text-gray-400">{{__('models/book_chapter.prop.time_spent')}}</div>
            <div>{{ human_diff($chapter->started, $chapter->finished) ?? '—' }}</div>
        </div>
    </div>
    <div class="flex flex-col gap-4">
        <div>
            <div class="text-gray-400">{{__('frontend.action.start')}}</div>
            <div>{{ human_date_with_time($chapter->started) ?? '—' }}</div>
        </div>
        <div>
            <div class="text-gray-400">{{__('frontend.action.ending')}}</div>
            <div>{{ human_date_with_time($chapter->finished) ?? '—' }}</div>
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
        <x-book-chapters.actions :chapter="$chapter"/>
    </div>
</div>
