<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Article Category Details</h2>
            <a href="{{ route('admin.article-categories.index') }}" class="rounded border px-3 py-1">Back</a>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-semibold">Category: {{ $id ?? '—' }}</h3>
            <p class="text-sm text-slate-600 mt-2">Details placeholder. Implement actual fields later.</p>
        </div>
    </div>
</x-app-layout>
