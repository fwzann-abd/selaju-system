<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span class="text-slate-900 dark:text-white">Mata Pelajaran</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Mata Pelajaran</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="{{ route('admin.subjects.index') }}" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode mata pelajaran"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    @if($search)
                        <a href="{{ route('admin.subjects.index') }}" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.subjects.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Mata Pelajaran
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @forelse($subjects as $subject)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm font-semibold">
                                    <a href="{{ route('admin.subjects.show', $subject) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">{{ $subject->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                    @if($subject->code)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-gray-600/10 ring-inset dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                            {{ $subject->code }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($subject->type)
                                        @php
                                            $typeColors = [
                                                'Vocational' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-600/10', 'darkBg' => 'dark:bg-indigo-400/10', 'darkText' => 'dark:text-indigo-400', 'darkRing' => 'dark:ring-indigo-400/20'],
                                                'Theory' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'ring' => 'ring-blue-600/10', 'darkBg' => 'dark:bg-blue-400/10', 'darkText' => 'dark:text-blue-400', 'darkRing' => 'dark:ring-blue-400/20'],
                                                'Practical' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'ring' => 'ring-green-600/10', 'darkBg' => 'dark:bg-green-400/10', 'darkText' => 'dark:text-green-400', 'darkRing' => 'dark:ring-green-400/20'],
                                                'Workshop' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'ring' => 'ring-purple-600/10', 'darkBg' => 'dark:bg-purple-400/10', 'darkText' => 'dark:text-purple-400', 'darkRing' => 'dark:ring-purple-400/20'],
                                            ];
                                            $colors = $typeColors[$subject->type] ?? $typeColors['Theory'];
                                        @endphp
                                        <span class="inline-flex items-center rounded-md {{ $colors['bg'] }} px-2 py-1 text-xs font-medium {{ $colors['text'] }} ring-1 {{ $colors['ring'] }} ring-inset {{ $colors['darkBg'] }} {{ $colors['darkText'] }} {{ $colors['darkRing'] }}">
                                            {{ $subject->type }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.subjects.edit', $subject) }}"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                           title="Edit mata pelajaran">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                                                    title="Hapus mata pelajaran">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data mata pelajaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $subjects->links() }}
    </div>
</x-app-layout>
