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
            <form action="<?php echo e(route('admin.eplin.officers.index')); ?>" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Cari nama atau NIS siswa"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <?php if($search): ?>
                        <a href="<?php echo e(route('admin.eplin.officers.index')); ?>" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('admin.eplin.officers.create')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Petugas
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jabatan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Mulai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Selesai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = $officers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $officer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
                                    <?php echo e($officer->student?->name ?? 'N/A'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php echo e($officer->student?->student_number ?? 'N/A'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                        <?php echo e(match($officer->role) {
                                            'petugas' => 'Petugas',
                                            default => $officer->role,
                                        }); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php echo e(\Carbon\Carbon::parse($officer->start_date)->format('d M Y')); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php if($officer->end_date): ?>
                                        <?php echo e(\Carbon\Carbon::parse($officer->end_date)->format('d M Y')); ?>

                                    <?php else: ?>
                                        <span class="text-slate-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if($officer->is_active): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            <span class="h-2 w-2 rounded-full bg-slate-600"></span>
                                            Tidak Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo e(route('admin.eplin.officers.edit', $officer)); ?>"
                                           class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-medium text-indigo-600 transition hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/20">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                            Edit
                                        </a>
                                        <form action="<?php echo e(route('admin.eplin.officers.destroy', $officer)); ?>" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus petugas ini?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/20">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Tidak ada petugas yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($officers->hasPages()): ?>
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                    <?php echo e($officers->links()); ?>

                </div>
            <?php endif; ?>
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
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/eplin/officers/index.blade.php ENDPATH**/ ?>