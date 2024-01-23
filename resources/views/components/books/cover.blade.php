<div class="w-56 shrink-0 space-y-4">
    <img class="rounded-2xl" src="{{ $cover }}" alt="">
    @if ($book->fileUrl())
        <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 w-full"
           href="{{ $book->fileUrl() }}">
            @lang('frontend.action.download')
        </a>
    @endif
    @foreach (($references = explode("\n", trim($book->reference_link))) as $key => $url)
        @if (blank($url))
            @continue
        @endif
        <a class="flex justify-center items-center gap-2 text-blue-800 py-2 px-3 rounded-lg border border-blue-800 w-full"
           href="{{ trim($url) }}" target="_blank">
            @lang('frontend.action.read_online') @if (count($references) > 1) #{{ $key + 1 }} @endif
        </a>
    @endforeach
</div>
