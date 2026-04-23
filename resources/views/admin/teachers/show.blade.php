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
        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('admin.teachers.edit', $teacher) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-pen-to-square"></i>
                Edit Guru
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            {{-- Info Utama --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informasi Guru</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Nama</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $teacher->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">NIP</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $teacher->nip ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Sekolah</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $teacher->school->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Akun --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informasi Akun</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Username</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $teacher->account->username ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Email</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $teacher->account->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Dibuat</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $teacher->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Jadwal Mengajar --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Jadwal Mengajar</h3>
            @if($teacher->schedules && $teacher->schedules->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Hari</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jam</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Mata Pelajaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach($teacher->schedules->sortBy('day') as $schedule)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-600/10 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">{{ $schedule->day }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">{{ $schedule->classroom->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $schedule->subject->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-calendar-xmark mb-2 text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p>Belum ada jadwal mengajar.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
