<div>
    <div class="text-gray-400">{{__('frontend.action.your_progress')}}</div>
    <div>{{ $progress }}%</div>
</div>
<div class="bg-yellow-100 rounded-full overflow-hidden shadow-sm">
    <div class="bg-yellow-500 h-2 w-10" style="width: {{ $progress }}%"></div>
</div>
