<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Dashboard</a>
                <span class="mx-2">/</span>
                <span>LMS Melesat</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Data Absensi</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Data Absensi KBM</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Flash messages --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Filter Absensi</h3>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="filter_classroom_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kelas</label>
                    <select id="filter_classroom_id" name="classroom_id"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Semua Kelas</option>
                        @foreach ($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected(request('classroom_id') == $classroom->id)>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal</label>
                    <input type="date" id="filter_date" name="date" value="{{ request('date') }}"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['classroom_id', 'date']))
                        <a href="{{ route('admin.attendances.index') }}"
                            class="rounded-xl bg-slate-200 px-4 py-2 font-medium text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition">
                            Hapus
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Log Information --}}
        <div class="flex justify-between items-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Total Rekaman: <span class="font-semibold text-slate-900 dark:text-white">{{ $attendances->total() }}</span>
            </p>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @if ($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas / Matpel</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach ($attendances as $attendance)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $attendance->date?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $attendance->student->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $attendance->student->student_number ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $attendance->schedule->classroom->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $attendance->schedule->subject->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $colorClass = match($attendance->status) {
                                                'present' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-400',
                                                'absent' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-400/10 dark:text-rose-400',
                                                'sick' => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400',
                                                'permission' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-400',
                                                default => 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-400/10 dark:text-slate-400',
                                            };
                                            $statusLabel = match($attendance->status) {
                                                'present' => 'Hadir',
                                                'absent' => 'Alpa',
                                                'sick' => 'Sakit',
                                                'permission' => 'Izin',
                                                default => $attendance->status,
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $colorClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $attendances->links() }}
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    <p>Belum ada data absensi untuk filter ini.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
