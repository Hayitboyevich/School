<div>
    <div class="text-gray-400">{{__('models/book.prop.page_count')}}</div>
    <div>{{$page}}</div>
</div>
<div>
    <div class="text-gray-400">{{__('models/book.relation.genres')}}:</div>
    <div>{{ $genres->pluck('name')->join(', ') }}</div>
</div>
<div>
    <div class="text-gray-400">{{__('models/book.prop.group_level')}}:</div>
    <div>{{ implode(', ', $level) }}</div>
</div>
<div>
    <div class="text-gray-400">{{__('models/book.relation.academic_years')}}:</div>
    <div>{{ $academicYears->pluck('period')->join(', ') }}</div>
</div>
