<x-app-layout>
    <div class="border p-10 shadow-sm rounded-2xl bg-white space-y-5 mx-auto ">
        <div class="flex gap-10">
            <x-books.cover :cover="$book->coverUrl()" :book="$book"/>
            <div class="space-y-5">
                <x-books.header :name="$book->name" :authors="$book->book_authors"/>
                <x-books.badge :status="$book->status"/>
                <x-books.reading-period :start="human_date_with_time($book->started) ?? human_date($book->start_date)" :end="human_date_with_time($book->finished) ?? human_date($book->end_date)"/>
                <x-books.progress :progress="$progress"/>
                <x-books.description :description="$book->description"/>
                <x-books.about :page="$book->page_count" :genres="$book->genres" :level="$book->groups->pluck('name')->toArray()" :academicYears="$book->academic_years"/>
            </div>
            <x-books.points :score="$book->score"/>
        </div>
    </div>
    @if($book->book_chapters->count() == 0)
        <x-books.action-panel :book="$book"/>
    @else
        <x-book-chapters.chapters :chapters="$book->book_chapters"/>
    @endif
</x-app-layout>
