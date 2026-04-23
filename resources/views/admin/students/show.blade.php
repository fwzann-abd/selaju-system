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
            <a href="{{ route('admin.students.edit', $student) }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-pen-to-square"></i>
                Edit Siswa
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            {{-- Info Utama --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informasi Siswa</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Nama</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $student->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">NIS</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->student_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">NISN</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->national_id ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Jenis Kelamin</dt>
                        <dd class="mt-1">
                            @if($student->gender === 'L')
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-600/10 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">Laki-laki</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-pink-50 px-2 py-1 text-xs font-medium text-pink-700 ring-1 ring-pink-600/10 ring-inset dark:bg-pink-400/10 dark:text-pink-400 dark:ring-pink-400/20">Perempuan</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Sekolah</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->school->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Info Akun --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Informasi Akun</h3>
                <dl class="space-y-4">
                    @if($student->account)
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Username</dt>
                            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->account->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Email</dt>
                            <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->account->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/10 ring-inset dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">Terdaftar</span>
                            </dd>
                        </div>
                    @else
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-yellow-600/10 ring-inset dark:bg-yellow-400/10 dark:text-yellow-400 dark:ring-yellow-400/20">Belum Terdaftar</span>
                            <p class="mt-2">Siswa belum memiliki akun.</p>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">Dibuat</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $student->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Kelas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="mb-4 text-base font-semibold text-slate-900 dark:text-white">Kelas yang Diikuti</h3>
            @if($student->classrooms && $student->classrooms->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                        <thead class="bg-slate-50 dark:bg-slate-800/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Posisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                            @foreach($student->classrooms as $classroom)
                                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-white">
                                        <a href="{{ route('admin.classrooms.show', $classroom) }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                            {{ $classroom->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $classroom->pivot->position ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-graduation-cap mb-2 text-2xl text-slate-300 dark:text-slate-600"></i>
                    <p>Siswa belum terdaftar di kelas manapun.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
