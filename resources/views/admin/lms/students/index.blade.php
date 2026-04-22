<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span class="text-slate-900 dark:text-white">Data Siswa</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">LMS Melesat</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Data Siswa</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="studentsTable()" x-init="init()">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form class="w-full md:max-w-sm" @submit.prevent="searchStudents">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" x-model="search" placeholder="Cari siswa..."
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-indigo-700">Cari</button>
                    <button type="button" x-show="search" @click="resetSearch()" class="text-xs text-indigo-500 hover:underline">Reset</button>
                </div>
            </form>

            <div class="flex flex-wrap gap-3">
                <button @click="openAssignModal()"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    Assign Siswa ke Kelas
                </button>
                <a href="{{ route('admin.students.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Buat Siswa Baru
                </a>
            </div>
        </div>

        <!-- Loading -->
        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 p-6">
            <div class="space-y-4">
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/2 animate-pulse"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-2/3 animate-pulse"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/3 animate-pulse"></div>
            </div>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Lengkap</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">NIS/NIM</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900" x-show="paginatedStudents.length > 0">
                        <template x-for="(student, index) in paginatedStudents" :key="student.id">
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="fromRecord + index"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                                            <span class="text-xs font-semibold" x-text="student.name?.charAt(0)?.toUpperCase() || '?'"></span>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="student.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="student.student_number || '-' "></td>
                                <td class="px-6 py-4">
                                    <span x-show="student.classrooms && student.classrooms !== '-'" class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300" x-text="student.classrooms"></span>
                                    <span x-show="!student.classrooms || student.classrooms === '-'" class="text-sm text-slate-400 dark:text-slate-500">Belum ada kelas</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="student.email || '-' "></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="editStudentAssignment(student)"
                                                class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                                title="Edit Kelas">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button @click="removeStudentFromClass(student)"
                                                class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                                                title="Hapus dari Kelas">
                                            <i class="fa-solid fa-user-minus text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!paginatedStudents.length && !loading" class="px-6 py-12 text-center">
                <i class="fa-solid fa-users text-4xl text-slate-300 dark:text-slate-600 mb-4 inline-block"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada data siswa.</p>
            </div>

            <!-- Pagination -->
            <div x-show="paginatedStudents.length > 0" class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Menampilkan <span x-text="fromRecord"></span> sampai <span x-text="toRecord"></span> dari <span x-text="students.length"></span> siswa</p>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage()" :disabled="page === 1" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Sebelumnya</button>
                        <button @click="nextPage()" :disabled="page === totalPages" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Berikutnya</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Student Modal -->
        <div x-show="isAssignModalVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" x-transition.opacity>
            <div @click.away="isAssignModalVisible = false" class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Assign Siswa ke Kelas</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Pilih kelas dan cari siswa untuk ditambahkan.</p>
                    </div>
                    <button type="button" @click="isAssignModalVisible = false" class="rounded-full p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-4 px-6 py-6 text-sm text-slate-700 dark:text-slate-200">
                    <div>
                        <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas Tujuan</label>
                        <select x-model="assignData.classroom_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                            <option value="">Pilih kelas</option>
                            <template x-for="classroom in classrooms" :key="classroom.id">
                                <option :value="classroom.id" x-text="classroom.name"></option>
                            </template>
                        </select>
                        <p x-text="assignErrors.classroom_id ?? ''" class="mt-1 text-xs text-red-500"></p>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Cari Siswa</label>
                        <input type="text" x-model="studentSearchQuery" @input.debounce.300ms="searchAvailableStudents()"
                               placeholder="Ketik nama atau NIS siswa..."
                               class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                    </div>

                    <!-- Selected Students -->
                    <div x-show="assignData.students.length > 0" class="space-y-2">
                        <label class="block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Siswa Terpilih</label>
                        <template x-for="(s, idx) in assignData.students" :key="s.student_id">
                            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-800">
                                <span class="text-sm" x-text="s.name"></span>
                                <button @click="assignData.students.splice(idx, 1)" class="text-red-500 hover:text-red-700">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Search Results -->
                    <div x-show="availableStudents.length > 0" class="max-h-48 overflow-y-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <template x-for="s in availableStudents" :key="s.id">
                            <button @click="addStudentToAssign(s)" type="button"
                                    class="flex w-full items-center justify-between px-4 py-3 text-sm transition hover:bg-slate-50 dark:hover:bg-slate-800"
                                    :disabled="assignData.students.some(sel => sel.student_id === s.id)">
                                <div>
                                    <span class="font-medium" x-text="s.name"></span>
                                    <span class="ml-2 text-slate-400" x-text="s.student_number || ''"></span>
                                </div>
                                <span x-show="assignData.students.some(sel => sel.student_id === s.id)" class="text-xs text-emerald-500">
                                    <i class="fa-solid fa-check"></i> Terpilih
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800 sm:flex-row sm:justify-end">
                    <button type="button" @click="isAssignModalVisible = false" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</button>
                    <button type="button" @click="submitAssignment()" :disabled="saving" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!saving">Assign Siswa</span>
                        <span x-show="saving">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Untuk menambah data siswa baru, gunakan menu <a href="{{ route('admin.students.index') }}" class="font-medium underline">Sekolah → Data Siswa</a>.
            Halaman ini mengelola penempatan siswa ke kelas dalam LMS Melesat.
        </div>
    </div>

    <script>
        function studentsTable() {
            return {
                search: '',
                loading: true,
                students: [],
                classrooms: [],
                availableStudents: [],
                studentSearchQuery: '',
                page: 1,
                perPage: 10,
                isAssignModalVisible: false,
                saving: false,
                assignErrors: {},
                assignData: {
                    classroom_id: '',
                    students: [],
                },

                async init() {
                    await Promise.all([this.fetchStudents(), this.fetchClassrooms()]);
                },

                async fetchStudents() {
                    try {
                        this.loading = true;
                        const response = await axios.get('/api/lms/students', {
                            params: { search: this.search },
                            withCredentials: true,
                        });
                        this.students = response.data.data || [];
                    } catch (error) {
                        const message = error.response?.data?.message || error.message || 'Gagal memuat data siswa';
                        console.error('Error fetching students:', error);
                        Swal.fire('Error', message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchClassrooms() {
                    try {
                        const response = await axios.get('/api/lms/classrooms');
                        this.classrooms = response.data.data || response.data || [];
                    } catch (error) {
                        console.error('Error fetching classrooms:', error);
                        this.classrooms = [];
                    }
                },

                async searchStudents() {
                    this.page = 1;
                    await this.fetchStudents();
                },

                resetSearch() {
                    this.search = '';
                    this.page = 1;
                    this.fetchStudents();
                },

                async searchAvailableStudents() {
                    if (this.studentSearchQuery.length < 2) {
                        this.availableStudents = [];
                        return;
                    }
                    try {
                        const response = await axios.get('/api/lms/students', {
                            params: { search: this.studentSearchQuery },
                            withCredentials: true,
                        });
                        this.availableStudents = response.data.data || [];
                    } catch (error) {
                        console.error('Error searching students:', error);
                    }
                },

                addStudentToAssign(student) {
                    if (this.assignData.students.some(s => s.student_id === student.id)) return;
                    this.assignData.students.push({
                        student_id: student.id,
                        name: student.name,
                        student_position_id: null,
                    });
                },

                openAssignModal() {
                    this.assignData = { classroom_id: '', students: [] };
                    this.assignErrors = {};
                    this.studentSearchQuery = '';
                    this.availableStudents = [];
                    this.isAssignModalVisible = true;
                },

                async submitAssignment() {
                    if (!this.assignData.classroom_id) {
                        this.assignErrors = { classroom_id: 'Pilih kelas tujuan' };
                        return;
                    }
                    if (this.assignData.students.length === 0) {
                        Swal.fire('Peringatan', 'Pilih minimal 1 siswa untuk di-assign', 'warning');
                        return;
                    }

                    try {
                        this.saving = true;
                        this.assignErrors = {};
                        await axios.post('/api/lms/classrooms/assign-student', {
                            classroom_id: this.assignData.classroom_id,
                            students: this.assignData.students.map(s => ({
                                student_id: s.student_id,
                                student_position_id: s.student_position_id,
                            })),
                        });
                        this.isAssignModalVisible = false;
                        await Swal.fire('Sukses', 'Siswa berhasil di-assign ke kelas', 'success');
                        await this.fetchStudents();
                    } catch (error) {
                        if (error.response?.status === 422) {
                            this.assignErrors = error.response.data.errors || {};
                            Swal.fire('Periksa kembali data', 'Terdapat kesalahan input.', 'warning');
                        } else {
                            Swal.fire('Error', error.response?.data?.message || 'Gagal assign siswa', 'error');
                        }
                    } finally {
                        this.saving = false;
                    }
                },

                editStudentAssignment(student) {
                    if (!student.classrooms || student.classrooms === '-') {
                        Swal.fire('Info', 'Siswa ini belum memiliki kelas. Gunakan "Assign Siswa ke Kelas".', 'info');
                        return;
                    }
                    this.assignData = {
                        classroom_id: '',
                        students: [{ student_id: student.id, name: student.name, student_position_id: null }],
                    };
                    this.assignErrors = {};
                    this.studentSearchQuery = '';
                    this.availableStudents = [];
                    this.isAssignModalVisible = true;
                },

                async removeStudentFromClass(student) {
                    if (!student.classrooms || student.classrooms === '-') {
                        Swal.fire('Info', 'Siswa ini belum memiliki kelas.', 'info');
                        return;
                    }
                    const result = await Swal.fire({
                        title: 'Hapus dari Kelas?',
                        text: `Yakin ingin menghapus ${student.name} dari kelas?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                    });

                    if (!result.isConfirmed) return;

                    Swal.fire('Info', 'Fitur remove dari kelas akan diimplementasi di fase berikutnya.', 'info');
                },

                get filteredStudents() {
                    return this.students;
                },

                get paginatedStudents() {
                    const start = (this.page - 1) * this.perPage;
                    return this.filteredStudents.slice(start, start + this.perPage);
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredStudents.length / this.perPage));
                },

                get fromRecord() {
                    return this.paginatedStudents.length ? (this.page - 1) * this.perPage + 1 : 0;
                },

                get toRecord() {
                    return this.fromRecord + this.paginatedStudents.length - 1;
                },

                prevPage() { if (this.page > 1) this.page -= 1; },
                nextPage() { if (this.page < this.totalPages) this.page += 1; },
            }
        }
    </script>
</x-app-layout>
