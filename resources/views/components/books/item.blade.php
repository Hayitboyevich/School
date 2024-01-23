<div class="rounded-2xl overflow-hidden bg-white h-full">
    <a href="{{ route('books.show', $book->id) }}">
        <img src="{{ $book->coverUrl() }}" alt="">
    </a>
    <div class="p-4 space-y-4">
        <div>
            <a href="{{ route('books.show', $book->id) }}">
                <h4 class="font-semibold">{{ $book->name }}</h4>
            </a>
            <div>{{ $book->book_authors->pluck('full_name')->join(", ") }}</div>
            <div class="text-gray-400">{{ $book->genres->pluck('name')->join(', ') }}</div>
        </div>
        <x-books.badge :status="$book->status"/>
        <x-books.actions :book="$book"/>
    </div>
</div>
