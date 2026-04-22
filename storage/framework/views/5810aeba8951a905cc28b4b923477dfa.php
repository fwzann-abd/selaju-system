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
                <span>Admin</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Transfer Manual</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Transfer Manual</h2>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="space-y-6">
        <?php if(session('success')): ?>
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="<?php echo e(route('admin.manual-transfers.index')); ?>" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari nama donatur..."
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <?php if(request('q')): ?>
                        <a href="<?php echo e(route('admin.manual-transfers.index')); ?>" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Pendonasi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Bank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nomor Rekening</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Jumlah Transfer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tanggal Transfer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        <?php $__empty_1 = true; $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
                                    <?php echo e($transfer->donation->donor_name ?? 'N/A'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php echo e($transfer->bank_name); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php echo e($transfer->account_number); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                    Rp <?php echo e(number_format($transfer->transfer_amount, 0, ',', '.')); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php echo e($transfer->transfer_date->format('d M Y')); ?>

                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <?php if($transfer->verification_status === 'verified'): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                            Terverifikasi
                                        </span>
                                    <?php elseif($transfer->verification_status === 'rejected'): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                            <span class="h-2 w-2 rounded-full bg-red-600"></span>
                                            Ditolak
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">
                                            <span class="h-2 w-2 rounded-full bg-yellow-600"></span>
                                            Menunggu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="<?php echo e(route('admin.manual-transfers.show', $transfer->id)); ?>" class="font-medium text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data transfer manual
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if($transfers->hasPages()): ?>
            <div class="flex justify-center">
                <?php echo e($transfers->links()); ?>

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
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/manual-transfers/index.blade.php ENDPATH**/ ?>