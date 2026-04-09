<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span class="text-slate-900 dark:text-white">Daftar Kelas</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">LMS Melesat</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Daftar Kelas</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="searchForm()" x-init="init()">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form class="w-full md:max-w-sm" @submit.prevent="submitSearch">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" x-model="search" placeholder="Cari nama kelas, tingkat, atau jurusan"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <button type="button" x-show="search" @click="resetSearch()" class="text-xs text-indigo-500 hover:underline">
                        Reset
                    </button>
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <button 
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 disabled:bg-slate-400"
                    @click="openAddModal()"
                >
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Kelas
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900 p-6">
            <div class="space-y-4">
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-3/4"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/2"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-5/6"></div>
            </div>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tingkat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jurusan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Wali Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900" x-show="classrooms.length > 0">
                        <template x-for="(classroom, index) in filteredClassrooms" :key="classroom.id">
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    <span x-text="index + 1"></span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    <span x-text="classroom.name"></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span x-text="classroom.tingkat"></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span x-text="classroom.jurusan"></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span x-text="classroom.teacher?.name ?? '-'"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            @click="editClassroom(classroom)"
                                            class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                            title="Edit"
                                        >
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button 
                                            @click="deleteClassroom(classroom.id)"
                                            class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div x-show="classrooms.length === 0 && !loading" class="px-6 py-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Belum ada data kelas.
                </p>
            </div>
        </div>

        <div x-show="isAddModalVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" x-transition.opacity>
            <div @click.away="isAddModalVisible = false" class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Tambah Kelas</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Isi data kelas baru untuk ditambahkan ke database.</p>
                    </div>
                    <button type="button" @click="isAddModalVisible = false" class="rounded-full p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-4 px-6 py-6 text-sm text-slate-700 dark:text-slate-200">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Tingkat</label>
                            <input type="text" x-model="newClassroom.tingkat" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="10">
                            <p x-text="errors.tingkat ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Jurusan</label>
                            <input type="text" x-model="newClassroom.jurusan" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="PPLG">
                            <p x-text="errors.jurusan ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Rombel</label>
                            <input type="text" x-model="newClassroom.rombel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="1">
                            <p x-text="errors.rombel ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Tahun Ajaran</label>
                            <input type="text" x-model="newClassroom.academic_year" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="2025/2026">
                            <p x-text="errors.academic_year ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Wali Kelas</label>
                            <select x-model="newClassroom.teacher_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="">Pilih wali kelas</option>
                                <template x-for="teacher in teachers" :key="teacher.id">
                                    <option :value="teacher.id" x-text="teacher.name"></option>
                                </template>
                            </select>
                            <p x-text="errors.teacher_id ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800 sm:flex-row sm:justify-end">
                    <button type="button" @click="isAddModalVisible = false" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</button>
                    <button type="button" @click="createClassroom()" :disabled="saving" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!saving">Simpan</span>
                        <span x-show="saving">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="isEditModalVisible" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" x-transition.opacity>
            <div @click.away="isEditModalVisible = false" class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-slate-900">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Edit Kelas</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Perbarui data kelas yang dipilih.</p>
                    </div>
                    <button type="button" @click="isEditModalVisible = false" class="rounded-full p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-4 px-6 py-6 text-sm text-slate-700 dark:text-slate-200">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Tingkat</label>
                            <input type="text" x-model="editClassroom.tingkat" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="10">
                            <p x-text="editErrors.tingkat ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Jurusan</label>
                            <input type="text" x-model="editClassroom.jurusan" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="PPLG">
                            <p x-text="editErrors.jurusan ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Rombel</label>
                            <input type="text" x-model="editClassroom.rombel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="1">
                            <p x-text="editErrors.rombel ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Tahun Ajaran</label>
                            <input type="text" x-model="editClassroom.academic_year" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100" placeholder="2025/2026">
                            <p x-text="editErrors.academic_year ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-2 block text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">Wali Kelas</label>
                            <select x-model="editClassroom.teacher_id" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-indigo-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                                <option value="">Pilih wali kelas</option>
                                <template x-for="teacher in teachers" :key="teacher.id">
                                    <option :value="teacher.id" x-text="teacher.name"></option>
                                </template>
                            </select>
                            <p x-text="editErrors.teacher_id ?? ''" class="mt-1 text-xs text-red-500"></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800 sm:flex-row sm:justify-end">
                    <button type="button" @click="isEditModalVisible = false" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</button>
                    <button type="button" @click="updateClassroom()" :disabled="updating" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!updating">Simpan Perubahan</span>
                        <span x-show="updating">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchForm() {
            return {
                search: '',
                loading: true,
                classrooms: [],
                teachers: [],
                isAddModalVisible: false,
                isEditModalVisible: false,
                saving: false,
                updating: false,
                errors: {},
                editErrors: {},
                listeners: [],
                newClassroom: {
                    tingkat: '',
                    jurusan: '',
                    rombel: '',
                    teacher_id: '',
                    academic_year: '',
                },
                editClassroom: {
                    id: '',
                    tingkat: '',
                    jurusan: '',
                    rombel: '',
                    teacher_id: '',
                    academic_year: '',
                },

                async init() {
                    await Promise.all([this.fetchClassrooms(), this.fetchTeachers()]);
                },

                setupListeners() {
                    this.listeners.forEach(listener => {
                        if (listener) listener.stop();
                    });
                    this.listeners = [];

                    this.classrooms.forEach(classroom => {
                        if (window.LmsUtils) {
                            try {
                                const listener = window.LmsUtils.setupLmsClassroomListener(classroom.id);
                                this.listeners.push(listener);
                            } catch (error) {
                                console.error(`Failed to setup listener for classroom ${classroom.id}:`, error);
                            }
                        }
                    });
                },

                async fetchClassrooms() {
                    try {
                        this.loading = true;
                        const response = await axios.get('/api/lms/classrooms');
                        this.classrooms = response.data.data || response.data || [];
                        setTimeout(() => this.setupListeners(), 0);
                    } catch (error) {
                        const message = error.response?.data?.message || error.response?.statusText || error.message || 'Gagal memuat data kelas';
                        console.error('Error fetching classrooms:', error);
                        Swal.fire('Error', message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchTeachers() {
                    try {
                        const response = await axios.get('/api/lms/teachers');
                        this.teachers = response.data.data || response.data || [];
                    } catch (error) {
                        console.error('Error fetching teachers:', error);
                        this.teachers = [];
                    }
                },

                get filteredClassrooms() {
                    if (!this.search) return this.classrooms;

                    const query = this.search.toLowerCase();
                    return this.classrooms.filter(classroom =>
                        classroom.name?.toLowerCase().includes(query) ||
                        classroom.tingkat?.toLowerCase().includes(query) ||
                        classroom.jurusan?.toLowerCase().includes(query) ||
                        classroom.teacher?.name?.toLowerCase().includes(query)
                    );
                },

                submitSearch() {
                    // Search is handled by computed property
                },

                resetSearch() {
                    this.search = '';
                },

                resetForm() {
                    this.newClassroom = {
                        tingkat: '',
                        jurusan: '',
                        rombel: '',
                        teacher_id: '',
                        academic_year: '',
                    };
                    this.errors = {};
                },

                resetEditForm() {
                    this.editClassroom = {
                        id: '',
                        tingkat: '',
                        jurusan: '',
                        rombel: '',
                        teacher_id: '',
                        academic_year: '',
                    };
                    this.editErrors = {};
                },

                editClassroom(classroom) {
                    this.resetEditForm();
                    this.editClassroom = {
                        id: classroom.id,
                        tingkat: classroom.tingkat || '',
                        jurusan: classroom.jurusan || '',
                        rombel: classroom.rombel || '',
                        teacher_id: classroom.teacher_id || '',
                        academic_year: classroom.academic_year || '',
                    };
                    this.isEditModalVisible = true;
                },

                async updateClassroom() {
                    try {
                        this.updating = true;
                        this.editErrors = {};

                        const payload = {
                            ...this.editClassroom,
                            name: `${this.editClassroom.tingkat} ${this.editClassroom.jurusan} ${this.editClassroom.rombel}`,
                        };
                        delete payload.id;
                        const response = await axios.put(`/api/lms/classrooms/${this.editClassroom.id}`, payload);

                        this.isEditModalVisible = false;
                        await Swal.fire('Sukses', response.data.message || 'Kelas berhasil diperbarui', 'success');
                        await this.fetchClassrooms();
                        this.resetEditForm();
                    } catch (error) {
                        if (error.response?.status === 422) {
                            this.editErrors = error.response.data.errors || {};
                            Swal.fire('Periksa kembali data Anda', 'Terdapat beberapa kesalahan input.', 'warning');
                        } else {
                            const message = error.response?.data?.message || error.response?.statusText || error.message || 'Gagal memperbarui kelas';
                            Swal.fire('Error', message, 'error');
                        }
                    } finally {
                        this.updating = false;
                    }
                },

                async deleteClassroom(classroomId) {
                    const result = await Swal.fire({
                        title: 'Hapus Kelas?',
                        text: 'Yakin ingin menghapus kelas ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                    });

                    if (!result.isConfirmed) return;

                    try {
                        await axios.delete(`/api/lms/classrooms/${classroomId}`);
                        await Swal.fire('Sukses', 'Kelas berhasil dihapus', 'success');
                        await this.fetchClassrooms();
                    } catch (error) {
                        const message = error.response?.data?.message || error.response?.statusText || 'Gagal menghapus kelas';
                        Swal.fire('Error', message, 'error');
                    }
                },

                openAddModal() {
                    this.resetForm();
                    this.isAddModalVisible = true;
                },

                async createClassroom() {
                    try {
                        this.saving = true;
                        this.errors = {};

                        const payload = {
                            ...this.newClassroom,
                            name: `${this.newClassroom.tingkat} ${this.newClassroom.jurusan} ${this.newClassroom.rombel}`,
                        };
                        const response = await axios.post('/api/lms/classrooms', payload);

                        this.isAddModalVisible = false;
                        await Swal.fire('Sukses', response.data.message || 'Kelas berhasil dibuat', 'success');
                        await this.fetchClassrooms();
                        this.resetForm();
                    } catch (error) {
                        if (error.response?.status === 422) {
                            this.errors = error.response.data.errors || {};
                            Swal.fire('Periksa kembali data Anda', 'Terdapat beberapa kesalahan input.', 'warning');
                        } else {
                            const message = error.response?.data?.message || error.response?.statusText || error.message || 'Gagal menyimpan kelas';
                            Swal.fire('Error', message, 'error');
                        }
                    } finally {
                        this.saving = false;
                    }
                },

                destroy() {
                    this.listeners.forEach(listener => {
                        if (listener) listener.stop();
                    });
                }
            }
        }
    </script>
</x-app-layout>
