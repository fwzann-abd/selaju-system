@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'space-y-1']) }}>
        @foreach ((array) $messages as $message)
            @if (preg_match('/^(\d{3}):\s*(.+)$/', $message, $matches))
                {{-- Dispatch error code to global toast (delayed for mount timing) --}}
                <div x-data x-init="setTimeout(() => $dispatch('toast', { code: {{ $matches[1] }}, message: @js($matches[2]), type: 'error' }), 150)" class="hidden"></div>
                {{-- Persistent message badge --}}
                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/10 ring-inset dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">{{ $matches[2] }}</span>
            @else
                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/10 ring-inset dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">{{ $message }}</span>
            @endif
        @endforeach
    </div>
@endif
