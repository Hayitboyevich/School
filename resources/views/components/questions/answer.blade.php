@foreach($answers as $answer)
    <label class="border p-4 shadow-sm rounded-2xl bg-white flex gap-4 items-center cursor-pointer mb-5" for="{{ $answer->id }}">
        <input id="{{ $answer->id }}" name="answer" type="radio" value="{{ $answer->id }}" {{ $answer->is_selected ? 'checked' : '' }}>
        <span>{!! $answer->content !!}</span>
    </label>
@endforeach
