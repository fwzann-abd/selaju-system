@props(['status'])

@if ($status)
    <span {{ $attributes->merge(['class' => 'inline-block rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400']) }}>{{ $status }}</span>
@endif
