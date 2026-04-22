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
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white"><?php echo e($pageTitle); ?></h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <?php if(session('success')): ?>
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="<?php echo e(route('admin.schools.index')); ?>" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Cari nama atau slug"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <?php if($search): ?>
                        <a href="<?php echo e(route('admin.schools.index')); ?>" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('admin.schools.create')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Sekolah
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Diperbarui</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    <?php echo e($school->name); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    /<?php echo e($school->slug); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <?php echo e($school->updated_at?->format('d M Y H:i') ?? '-'); ?>

                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo e(route('admin.teachers.index', ['school_id' => $school->id])); ?>"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                           title="Kelola Guru">
                                            <i class="fa-solid fa-chalkboard-user text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.classrooms.index')); ?>"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white"
                                           title="Kelola Kelas">
                                            <i class="fa-solid fa-door-open text-xs"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.schools.edit', $school)); ?>"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="<?php echo e(route('admin.schools.destroy', $school)); ?>" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus sekolah ini?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data sekolah.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($schools->hasPages()): ?>
            <div class="flex justify-center">
                <?php echo e($schools->links()); ?>

            </div>
        <?php endif; ?>
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
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/schools/index.blade.php ENDPATH**/ ?>