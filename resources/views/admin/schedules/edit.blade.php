<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Dashboard</a>
                <span class="mx-2">/</span>
                <span>LMS Melesat</span>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.schedules.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Jadwal KBM</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Edit</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle }}</h2>
        </div>
    </x-slot>

    @include('admin.schedules._form', [
        'action' => route('admin.schedules.update', $schedule),
        'method' => 'PUT',
        'buttonLabel' => 'Perbarui Jadwal',
    ])
</x-app-layout>
