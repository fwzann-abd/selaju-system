<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span class="text-slate-900 dark:text-white">Data Siswa</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">LMS Melesat</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Data Siswa</h2>
        </div>
     <?php $__env->endSlot(); ?>

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

            <button @click="showAddStudent()"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Siswa
            </button>
        </div>

        <div x-show="loading" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 p-6">
            <div class="space-y-4">
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/2"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-2/3"></div>
                <div class="h-4 bg-slate-200 rounded dark:bg-slate-700 w-1/3"></div>
            </div>
        </div>

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
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="student.name"></td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="student.student_number || '-' "></td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="student.classrooms || '-' "></td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400" x-text="student.email || '-' "></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button @click="editStudent(student)"
                                                class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                                title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <button @click="deleteStudent(student.id)"
                                                class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200"
                                                title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="!paginatedStudents.length && !loading" class="px-6 py-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada data siswa.</p>
            </div>

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
    </div>

    <script>
        function studentsTable() {
            return {
                search: '',
                loading: true,
                students: [],
                page: 1,
                perPage: 10,

                async init() {
                    await this.fetchStudents();
                },

                async fetchStudents() {
                    try {
                        this.loading = true;
                        const response = await axios.get('/api/lms/students', {
                            params: {
                                search: this.search,
                            },
                            withCredentials: true,
                        });
                        this.students = response.data.data || [];
                    } catch (error) {
                        const message = error.response?.data?.message || error.response?.statusText || error.message || 'Gagal memuat data siswa';
                        console.error('Error fetching students:', error);
                        Swal.fire('Error', message, 'error');
                    } finally {
                        this.loading = false;
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

                prevPage() {
                    if (this.page > 1) {
                        this.page -= 1;
                    }
                },

                nextPage() {
                    if (this.page < this.totalPages) {
                        this.page += 1;
                    }
                },

                editStudent(student) {
                    Swal.fire('Info', `Fitur edit siswa belum tersedia. ID: ${student.id}`, 'info');
                },

                deleteStudent(studentId) {
                    Swal.fire('Info', `Fitur hapus siswa belum tersedia. ID: ${studentId}`, 'info');
                },

                showAddStudent() {
                    Swal.fire('Info', 'Fitur tambah siswa belum tersedia.', 'info');
                },
            }
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\folder-v2\resources\views/admin/lms/students/index.blade.php ENDPATH**/ ?>