@props(['status'])

@if ($status)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/10 ring-inset dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20']) }}>{{ $status }}</span>
@endif
