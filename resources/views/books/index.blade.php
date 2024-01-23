<x-app-layout>
    <h2 class="text-4xl">@lang('models/book.plural')</h2>
    <x-books.filter/>
    <div class="grid grid-cols-4 gap-5">
        @foreach ($books as $book)
            <x-books.item :book="$book"/>
        @endforeach
    </div>
    <x-pagination :items="$books"/>
</x-app-layout>
