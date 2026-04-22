<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span class="text-slate-900 dark:text-white">Data Guru</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">LMS Melesat</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Data Guru</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="teachersTable()" x-init="init()">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form class="w-full md:max-w-sm" @submit.prevent="searchTeachers">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" x-model="search" placeholder="Cari nama guru atau NIP..."
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <button type="button" x-show="search" @click="resetSearch()" class="text-xs text-indigo-500 hover:underline">
                        Reset
                    </button>
                </div>
            </form>
            <a href="{{ route('admin.teachers.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Guru
            </a>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900 p-6">
            <div class="space-y-4">
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-3/4 animate-pulse"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/2 animate-pulse"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-5/6 animate-pulse"></div>
            </div>
        </div>

        <!-- Table -->
        <div x-show="!loading" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">NIP</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Sekolah</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kelas Diampu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900" x-show="filteredTeachers.length > 0">
                        <template x-for="(teacher, index) in filteredTeachers" :key="teacher.id">
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="index + 1"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                                            <span class="text-xs font-semibold" x-text="teacher.name?.charAt(0)?.toUpperCase() || '?'"></span>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="teacher.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="teacher.nip || '-'"></td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="teacher.school"></td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="teacher.email"></td>
                                <td class="px-6 py-4">
                                    <span x-show="teacher.classrooms !== '-'" class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300" x-text="teacher.classrooms"></span>
                                    <span x-show="teacher.classrooms === '-'" class="text-sm text-slate-400 dark:text-slate-500">Belum ada kelas</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div x-show="filteredTeachers.length === 0 && !loading" class="px-6 py-12 text-center">
                <i class="fa-solid fa-chalkboard-teacher text-4xl text-slate-300 dark:text-slate-600 mb-4 inline-block"></i>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Belum ada data guru. <a href="{{ route('admin.teachers.create') }}" class="text-indigo-500 hover:underline">Tambah guru baru</a>
                </p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Untuk menambah, edit, atau hapus data guru, gunakan menu <a href="{{ route('admin.teachers.index') }}" class="font-medium underline">Sekolah → Daftar Guru</a>.
            Halaman ini menampilkan guru dalam konteks LMS Melesat.
        </div>
    </div>

    <script>
        function teachersTable() {
            return {
                search: '',
                loading: true,
                teachers: [],

                async init() {
                    await this.fetchTeachers();
                },

                async fetchTeachers() {
                    try {
                        this.loading = true;
                        const response = await axios.get('/api/lms/teachers');
                        this.teachers = response.data.data || [];
                    } catch (error) {
                        const message = error.response?.data?.message || error.message || 'Gagal memuat data guru';
                        console.error('Error fetching teachers:', error);
                        Swal.fire('Error', message, 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async searchTeachers() {
                    try {
                        this.loading = true;
                        const response = await axios.get('/api/lms/teachers', {
                            params: { search: this.search },
                        });
                        this.teachers = response.data.data || [];
                    } catch (error) {
                        console.error('Error searching teachers:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                resetSearch() {
                    this.search = '';
                    this.fetchTeachers();
                },

                get filteredTeachers() {
                    if (!this.search) return this.teachers;
                    const query = this.search.toLowerCase();
                    return this.teachers.filter(t =>
                        t.name?.toLowerCase().includes(query) ||
                        t.nip?.toLowerCase().includes(query) ||
                        t.school?.toLowerCase().includes(query)
                    );
                },
            }
        }
    </script>
</x-app-layout>
