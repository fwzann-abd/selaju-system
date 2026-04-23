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
        @if(session('success'))
            <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-500/30 dark:bg-green-500/10 dark:text-green-200">
                <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

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
                    @if($availableStudents->count() > 0)
                        <button type="button" onclick="document.getElementById('assignModal').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600">
                            <i class="fa-solid fa-user-plus text-xs"></i>
                            Tambah Siswa
                        </button>
                    @endif
                </div>

                <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 bg-white text-sm dark:divide-slate-800 dark:bg-slate-950">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                                <tr>
                                    <th class="px-5 py-3">Nama Murid</th>
                                    <th class="px-5 py-3">NIS</th>
                                    <th class="px-5 py-3">Jabatan</th>
                                    <th class="px-5 py-3">Aksi</th>
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
                                        <td class="px-5 py-4">
                                            <form method="POST" action="{{ route('admin.classrooms.bulk-assign', $classroom) }}"
                                                  onsubmit="return confirm('Yakin ingin mengeluarkan siswa ini dari kelas?')">
                                                @csrf
                                                <input type="hidden" name="_remove" value="1">
                                                <input type="hidden" name="student_id" value="{{ $classroomStudent->student_id }}">
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition text-xs">
                                                    <i class="fa-solid fa-user-minus"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
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

    <!-- Bulk Assign Modal -->
    <div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Tambah Siswa ke Kelas</h3>
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.classrooms.bulk-assign', $classroom) }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Jabatan (opsional)
                    </label>
                    <select name="position_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Tanpa Jabatan --</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Pilih Siswa <span class="text-red-500">*</span>
                    </label>

                    <div class="mb-3">
                        <input type="text" id="studentSearch" placeholder="Cari siswa..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    </div>

                    <div class="flex gap-2 mb-3">
                        <button type="button" onclick="toggleAllStudents(true)"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 font-medium">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <button type="button" onclick="toggleAllStudents(false)"
                            class="text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 font-medium">
                            Hapus Semua
                        </button>
                        <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">
                            <span id="selectedCount">0</span> dipilih
                        </span>
                    </div>

                    <div class="max-h-64 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($availableStudents as $student)
                            <label class="student-item flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition"
                                   data-name="{{ strtolower($student->name) }}" data-nis="{{ $student->student_number }}">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                    class="student-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800"
                                    onchange="updateCount()">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $student->student_number ?? 'Tanpa NIS' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-4 flex gap-3">
                    <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')"
                        class="flex-1 rounded-lg border border-slate-300 px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 transition">
                        <i class="fa-solid fa-user-plus mr-1"></i>
                        Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('studentSearch')?.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.student-item').forEach(item => {
                const name = item.dataset.name || '';
                const nis = item.dataset.nis || '';
                item.style.display = (name.includes(query) || nis.includes(query)) ? '' : 'none';
            });
        });

        function toggleAllStudents(checked) {
            document.querySelectorAll('.student-checkbox').forEach(cb => {
                if (cb.closest('.student-item').style.display !== 'none') {
                    cb.checked = checked;
                }
            });
            updateCount();
        }

        function updateCount() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            const el = document.getElementById('selectedCount');
            if (el) el.textContent = count;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('assignModal');
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        };
    </script>
</x-app-layout>
