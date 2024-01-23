<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        {!! app('livewire')->mount($getState()['table'], $getState()['params']) !!}
    </div>
</x-dynamic-component>
