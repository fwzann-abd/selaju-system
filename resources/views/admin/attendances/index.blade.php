<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @foreach($breadcrumb as $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if($item['url'])
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-300">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                        <span class="mx-2">/</span>
                    @endif
                @endforeach
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle }}</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.attendances.index') }}">
            <div class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="w-full md:max-w-xs">
                    <label for="filter_classroom_id" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Kelas</label>
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

                <div class="w-full md:max-w-[180px]">
                    <label for="filter_date" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Tanggal</label>
                    <input type="date" id="filter_date" name="date" value="{{ request('date') }}"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div class="w-full md:max-w-[160px]">
                    <label for="filter_status" class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Status</label>
                    <select id="filter_status" name="status"
                        class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Semua</option>
                        <option value="present" @selected(request('status') === 'present')>Hadir</option>
                        <option value="absent" @selected(request('status') === 'absent')>Alpa</option>
                        <option value="sick" @selected(request('status') === 'sick')>Sakit</option>
                        <option value="permission" @selected(request('status') === 'permission')>Izin</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        <i class="fa-solid fa-filter text-xs"></i>
                        Filter
                    </button>
                    @if (request()->hasAny(['classroom_id', 'date', 'status']))
                        <a href="{{ route('admin.attendances.index') }}"
                            class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Summary --}}
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Menampilkan <span class="font-semibold text-slate-900 dark:text-white">{{ $attendances->total() }}</span> rekaman absensi
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
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas / Mata Pelajaran</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Guru</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach ($attendances as $attendance)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $attendance->date?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $attendance->student?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $attendance->student?->student_number ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ $attendance->schedule?->classroom?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $attendance->schedule?->subject?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        {{ $attendance->schedule?->teacher?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusConfig = match($attendance->status) {
                                                'present' => ['class' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20', 'label' => 'Hadir'],
                                                'absent' => ['class' => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20', 'label' => 'Alpa'],
                                                'sick' => ['class' => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20', 'label' => 'Sakit'],
                                                'permission' => ['class' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/10 dark:bg-yellow-400/10 dark:text-yellow-400 dark:ring-yellow-400/20', 'label' => 'Izin'],
                                                default => ['class' => 'bg-gray-50 text-gray-700 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20', 'label' => $attendance->status],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusConfig['class'] }}">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                    {{ $attendances->links() }}
                </div>
            @else
                <div class="flex flex-col items-center gap-3 py-16 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800">
                        <i class="fa-solid fa-clipboard-list text-lg text-slate-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Belum ada data absensi</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">Data absensi akan muncul setelah guru melakukan input melalui aplikasi mobile.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
