<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <span class="text-sm text-slate-500 dark:text-slate-400">Ringkasan hari ini</span>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Dashboard Admin</h2>
        </div>
    </x-slot>

    <div class="space-y-8">
        <section class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($metrics as $metric)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $metric['label'] }}</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($metric['value']) }}</p>
                        </div>
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                            <x-icon :name="$metric['icon']" class="h-6 w-6" />
                        </span>
                    </div>
                    <p class="mt-4 text-xs text-slate-400 dark:text-slate-500">{{ $metric['description'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Aktivitas Cepat</h3>
                    <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-300">Lihat semua</button>
                </div>
                <ul class="mt-6 space-y-4 text-sm">
                    @foreach ($quickActions as $index => $action)
                        <li class="flex items-start gap-3">
                            <span class="mt-1 inline-flex h-6 w-6 flex-none items-center justify-center rounded-full bg-indigo-500/10 text-xs font-semibold text-indigo-600 dark:text-indigo-300">{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <p class="font-medium text-slate-800 dark:text-slate-200">{{ $action['title'] }}</p>
                                <p class="text-slate-500 dark:text-slate-400">{{ $action['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Catatan Sistem</h3>
                <div class="flex flex-col gap-3 text-sm text-slate-500 dark:text-slate-400">
                    @foreach ($systemNotes as $note)
                        <div class="rounded-xl bg-slate-100 px-4 py-3 dark:bg-slate-800/60">
                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $note['title'] }}</p>
                            <p class="text-xs">{{ $note['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        @php
            $statusClasses = [
                'success' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200',
                'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-200',
                'info' => 'bg-sky-100 text-sky-700 dark:bg-sky-500/10 dark:text-sky-200',
            ];
        @endphp

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Aktivitas Terbaru</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Rangkuman proses penting 24 jam terakhir.</p>
                </div>
                <button type="button" class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-indigo-200 hover:text-indigo-600 dark:border-slate-700 dark:text-slate-300 dark:hover:border-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 006.582 9H4m16 0a8.003 8.003 0 01-7 7.938V20m0-3a8.003 8.003 0 01-7-7.938" />
                    </svg>
                    Refresh
                </button>
            </div>

            <ul class="mt-6 divide-y divide-slate-200 dark:divide-slate-800">
                @foreach ($recentActivities as $activity)
                    @php
                        $badgeClass = $statusClasses[$activity['status']] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800/60 dark:text-slate-300';
                    @endphp
                    <li class="flex flex-wrap items-center justify-between gap-4 py-4">
                        <div>
                            <p class="font-medium text-slate-800 dark:text-slate-200">{{ $activity['title'] }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $activity['timestamp'] }}</p>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                            <span class="h-2 w-2 rounded-full bg-current"></span>
                            {{ $activity['status_label'] }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-app-layout>
