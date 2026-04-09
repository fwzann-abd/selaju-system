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
                <span class="text-slate-900 dark:text-white">Jadwal KBM</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">LMS Melesat</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Jadwal KBM</h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <!-- Filter Card -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Filter Jadwal</h3>
            
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Kelas
                    </label>
                    <select id="classroom_id" name="classroom_id" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua Kelas</option>
                        <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($classroom->id); ?>" 
                                <?php echo e(request('classroom_id') == $classroom->id ? 'selected' : ''); ?>>
                                <?php echo e($classroom->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Guru
                    </label>
                    <select id="teacher_id" name="teacher_id" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua Guru</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->id); ?>" 
                                <?php echo e(request('teacher_id') == $teacher->id ? 'selected' : ''); ?>>
                                <?php echo e($teacher->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label for="day" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Hari
                    </label>
                    <select id="day" name="day" 
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Semua Hari</option>
                        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($day); ?>" 
                                <?php echo e(request('day') == $day ? 'selected' : ''); ?>>
                                <?php echo e($day); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" 
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 transition">
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Filter
                    </button>
                    <?php if(request()->filled(['classroom_id', 'teacher_id', 'day'])): ?>
                        <a href="<?php echo e(route('admin.lms.schedules.index')); ?>" 
                            class="rounded-lg bg-slate-300 px-4 py-2 font-medium text-slate-700 hover:bg-slate-400 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Add Schedule Button -->
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Total Jadwal: <span class="font-semibold text-slate-900 dark:text-white"><?php echo e($schedules->count()); ?></span>
                </p>
            </div>
            <button type="button" onclick="openAddModal()" 
                class="rounded-lg bg-green-600 px-6 py-2 font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600 transition">
                <i class="fa-solid fa-plus mr-2"></i>
                Tambah Jadwal
            </button>
        </div>

        <!-- Schedules Table -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
            <?php if($schedules->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Hari</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Jam</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Kelas</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Guru</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Mapel</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Ruangan</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
                                        <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-100">
                                            <?php echo e($schedule->day); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        <?php echo e($schedule->start_time); ?> - <?php echo e($schedule->end_time); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100 font-medium">
                                        <?php echo e($schedule->classroom->name ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        <?php echo e($schedule->teacher->name ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        <?php echo e($schedule->subject->name ?? '-'); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        <?php if($schedule->room): ?>
                                            <span class="inline-block rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800 dark:bg-purple-900 dark:text-purple-100">
                                                <?php echo e($schedule->room->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex gap-2">
                                            <button type="button" 
                                                onclick="openEditModal(<?php echo e($schedule->toJson()); ?>)"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 transition">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form method="POST" action="<?php echo e(route('admin.lms.schedules.destroy', $schedule)); ?>" 
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="rounded-xl p-12 text-center">
                    <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-slate-600 mb-4 inline-block"></i>
                    <p class="text-slate-500 dark:text-slate-400">
                        Belum ada jadwal KBM. Klik tombol "Tambah Jadwal" untuk membuat jadwal baru.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Add/Edit Schedule -->
    <div id="scheduleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 dark:bg-opacity-70 p-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center sticky top-0 bg-white dark:bg-slate-900">
                <h3 id="modalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">
                    Tambah Jadwal KBM
                </h3>
                <button type="button" onclick="closeModal()" 
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="scheduleForm" method="POST" action="<?php echo e(route('admin.lms.schedules.store')); ?>" class="p-6 space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="scheduleId" name="schedule_id">
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div>
                    <label for="classroom_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select id="classroom_id" name="classroom_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Pilih Kelas</option>
                        <?php $__currentLoopData = $classrooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($classroom->id); ?>"><?php echo e($classroom->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="classroom_id-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                </div>

                <div>
                    <label for="teacher_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Guru <span class="text-red-500">*</span>
                    </label>
                    <select id="teacher_id" name="teacher_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Pilih Guru</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="teacher_id-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select id="subject_id" name="subject_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Pilih Mata Pelajaran</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->id); ?>"><?php echo e($subject->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="subject_id-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                </div>

                <div>
                    <label for="room_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Ruangan
                    </label>
                    <select id="room_id" name="room_id"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Pilih Ruangan</option>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($room->id); ?>"><?php echo e($room->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="room_id-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                </div>

                <div>
                    <label for="day" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Hari <span class="text-red-500">*</span>
                    </label>
                    <select id="day" name="day" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <option value="">Pilih Hari</option>
                        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($day); ?>"><?php echo e($day); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span id="day-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="start_time" name="start_time" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <span id="start_time-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="end_time" name="end_time" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                        <span id="end_time-error" class="text-sm text-red-600 dark:text-red-400 hidden mt-1 block"></span>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-4 flex gap-3 sticky bottom-0 bg-white dark:bg-slate-900">
                    <button type="button" onclick="closeModal()" 
                        class="flex-1 rounded-lg border border-slate-300 px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit" 
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').innerText = 'Tambah Jadwal KBM';
            document.getElementById('scheduleForm').action = '<?php echo e(route("admin.lms.schedules.store")); ?>';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('scheduleForm').reset();
            clearErrors();
            document.getElementById('scheduleModal').classList.remove('hidden');
        }

        function openEditModal(schedule) {
            document.getElementById('modalTitle').innerText = 'Edit Jadwal KBM';
            document.getElementById('scheduleForm').action = `/admin/lms/schedules/${schedule.id}`;
            document.getElementById('formMethod').value = 'PATCH';
            
            document.getElementById('classroom_id').value = schedule.classroom_id;
            document.getElementById('teacher_id').value = schedule.teacher_id;
            document.getElementById('subject_id').value = schedule.subject_id;
            document.getElementById('room_id').value = schedule.room_id || '';
            document.getElementById('day').value = schedule.day;
            document.getElementById('start_time').value = schedule.start_time;
            document.getElementById('end_time').value = schedule.end_time;
            
            clearErrors();
            document.getElementById('scheduleModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
            document.getElementById('scheduleForm').reset();
            clearErrors();
        }

        function clearErrors() {
            document.querySelectorAll('[id$="-error"]').forEach(el => {
                el.classList.add('hidden');
                el.innerText = '';
            });
        }

        document.getElementById('scheduleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const formData = new FormData(this);
            const method = document.getElementById('formMethod').value;
            const action = this.action;

            try {
                const response = await fetch(action, {
                    method: method === 'PATCH' ? 'POST' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (!response.ok) {
                    const errors = await response.json();
                    if (errors.errors) {
                        Object.keys(errors.errors).forEach(field => {
                            const errorElement = document.getElementById(`${field}-error`);
                            if (errorElement) {
                                errorElement.innerText = errors.errors[field][0];
                                errorElement.classList.remove('hidden');
                            }
                        });
                    }
                    return;
                }

                window.location.reload();
            } catch (error) {
                console.error('Error:', error);
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('scheduleModal');
            if (event.target === modal) {
                closeModal();
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
<?php /**PATH C:\fwzan\selaju-system\resources\views/admin/lms/schedules/index.blade.php ENDPATH**/ ?>