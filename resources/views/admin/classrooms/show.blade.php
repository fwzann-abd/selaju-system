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
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Detail Kelas {{ $classroom->name }}</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('admin.classrooms.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Kembali ke daftar kelas
            </a>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                Jumlah murid: <span class="font-semibold text-slate-900 dark:text-white">{{ $classroom->classroomStudents->count() }}</span>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Informasi Kelas</h3>
                <dl class="mt-4 space-y-4 text-sm text-slate-700 dark:text-slate-200">
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Nama kelas</dt>
                        <dd>{{ $classroom->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Tingkat</dt>
                        <dd>{{ $classroom->tingkat ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Jurusan</dt>
                        <dd>{{ $classroom->jurusan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Tahun ajaran</dt>
                        <dd>{{ $classroom->academic_year ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Wali kelas</dt>
                        <dd>{{ $classroom->teacher->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Daftar Murid</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tampilkan siswa di kelas ini beserta jabatan mereka.</p>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 bg-white text-sm dark:divide-slate-800 dark:bg-slate-950">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                                <tr>
                                    <th class="px-5 py-3">Nama Murid</th>
                                    <th class="px-5 py-3">NIS</th>
                                    <th class="px-5 py-3">Jabatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($classroom->classroomStudents as $classroomStudent)
                                    <tr>
                                        <td class="px-5 py-4 text-slate-800 dark:text-slate-100">
                                            {{ $classroomStudent->student->name ?? 'Tidak tersedia' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                            {{ $classroomStudent->student->student_number ?? '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                            {{ $classroomStudent->position->name ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Belum ada siswa yang terdaftar di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
