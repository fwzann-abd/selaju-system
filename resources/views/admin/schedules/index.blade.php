<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Dashboard</a>
                <span class="mx-2">/</span>
                <span>LMS Melesat</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Jadwal KBM</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Jadwal KBM</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Flash messages --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl border border-rose-300/40 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Filter Jadwal</h3>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label for="filter_teacher_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Guru</label>
                    <select id="filter_teacher_id" name="teacher_id"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Semua Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_day" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hari</label>
                    <select id="filter_day" name="day"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Semua Hari</option>
                        @foreach ($days as $day)
                            <option value="{{ $day }}" @selected(request('day') == $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 rounded-xl bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Filter
                    </button>
                    @if (request()->hasAny(['classroom_id', 'teacher_id', 'day']))
                        <a href="{{ route('admin.schedules.index') }}"
                            class="rounded-xl bg-slate-200 px-4 py-2 font-medium text-slate-700 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Summary + Add Button --}}
        <div class="flex justify-between items-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Total Jadwal: <span class="font-semibold text-slate-900 dark:text-white">{{ $schedules->count() }}</span>
            </p>
            <a href="{{ route('admin.schedules.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus"></i>
                Tambah Jadwal
            </a>
        </div>

        {{-- Schedules Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @if ($schedules->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Hari</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jam</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Guru</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Mapel</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Ruangan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach ($schedules as $schedule)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-600/10 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">{{ $schedule->day }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                        {{ $schedule->classroom->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $schedule->teacher->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $schedule->subject->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        @if ($schedule->room)
                                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-purple-600/10 ring-inset dark:bg-purple-400/10 dark:text-purple-400 dark:ring-purple-400/20">{{ $schedule->room->name }}</span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                                class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                                                class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-calendar-xmark mb-2 text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p>Belum ada jadwal KBM. Klik tombol 'Tambah Jadwal' untuk membuat jadwal baru.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>