@props(['title' => '', 'subtitle' => ''])

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    @if($title)
        <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">{{ $title }}</h3>
    @endif
    @if($subtitle)
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
    @endif
    <div @class(['mt-4' => $title || $subtitle])>
        {{ $slot }}
    </div>
</div>
