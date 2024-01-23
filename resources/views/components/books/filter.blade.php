<div class="flex items-center justify-between">
    @php($status = request()->query('status'))
    <div class="flex items-center gap-2">
        <x-books.filter-status :label="__('frontend.action.all')" :url="route('books.index', ['status' => null])" :selected="$status == null"/>
        <x-books.filter-status :label="__('frontend.action.currently_reading')" :url="route('books.index', ['status' => 'started'])" :selected="$status == 'started'"/>
        <x-books.filter-status :label="__('frontend.action.stopped')" :url="route('books.index', ['status' => 'paused'])" :selected="$status == 'paused'"/>
        <x-books.filter-status :label="__('frontend.action.finished')" :url="route('books.index', ['status' => 'finished'])" :selected="$status == 'finished'"/>
    </div>
</div>
