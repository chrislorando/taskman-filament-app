@php
    $record = $getRecord();
    $level = $record->getLevel(); // Ambil kedalaman level
@endphp

<div 
    @class([

        'w-full text-left ' 
    ])
    style="margin-left: {{ $level * 2 }}rem; "
>
    <div class="flex items-center gap-2 mb-2">
        <span class="font-bold text-sm @if($level == 0) text-primary-600 @endif">
            {{ $record->user->name }}
        </span>
  
        <span class="text-xs text-gray-500 italic">
            {{ $record->created_at->diffForHumans() }}
        </span>
    </div>

    <div class="prose prose-sm dark:prose-invert max-w-none">
        {!! str($record->body)->markdown() !!}
    </div>
</div>