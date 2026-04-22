@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <span class="inline-block rounded-full bg-red-50 px-2 py-0.5 text-xs text-red-600 dark:bg-red-500/10 dark:text-red-400">{{ $message }}</span>
        @endforeach
    </div>
@endif
