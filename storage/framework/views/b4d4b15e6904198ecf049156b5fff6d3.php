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
                <?php $__currentLoopData = $breadcrumb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($loop->last): ?>
                        <span class="text-slate-900 dark:text-white"><?php echo e($item['label']); ?></span>
                    <?php else: ?>
                        <?php if($item['url']): ?>
                            <a href="<?php echo e($item['url']); ?>" class="hover:text-slate-700 dark:hover:text-slate-300"><?php echo e($item['label']); ?></a>
                        <?php else: ?>
                            <span><?php echo e($item['label']); ?></span>
                        <?php endif; ?>
                        <span class="mx-2">/</span>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Detail Kelas <?php echo e($classroom->name); ?></h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="<?php echo e(route('admin.classrooms.index')); ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Kembali ke daftar kelas
            </a>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                Jumlah murid: <span class="font-semibold text-slate-900 dark:text-white"><?php echo e($classroom->classroomStudents->count()); ?></span>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Informasi Kelas</h3>
                <dl class="mt-4 space-y-4 text-sm text-slate-700 dark:text-slate-200">
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Nama kelas</dt>
                        <dd><?php echo e($classroom->name); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Tingkat</dt>
                        <dd><?php echo e($classroom->tingkat ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Jurusan</dt>
                        <dd><?php echo e($classroom->jurusan ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Tahun ajaran</dt>
                        <dd><?php echo e($classroom->academic_year ?? '-'); ?></dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900 dark:text-white">Wali kelas</dt>
                        <dd><?php echo e($classroom->teacher->name ?? '-'); ?></dd>
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
                                <?php $__empty_1 = true; $__currentLoopData = $classroom->classroomStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classroomStudent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="px-5 py-4 text-slate-800 dark:text-slate-100">
                                            <?php echo e($classroomStudent->student->name ?? 'Tidak tersedia'); ?>

                                        </td>
                                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                            <?php echo e($classroomStudent->student->student_number ?? '-'); ?>

                                        </td>
                                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                            <?php echo e($classroomStudent->position->name ?? '-'); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                            Belum ada siswa yang terdaftar di kelas ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
<?php /**PATH D:\shei\folder-v2\resources\views/admin/classrooms/show.blade.php ENDPATH**/ ?>