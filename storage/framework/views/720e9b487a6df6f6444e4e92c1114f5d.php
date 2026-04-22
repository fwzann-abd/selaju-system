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

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="space-y-6">
        <?php if(session('success')): ?>
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="<?php echo e(route('admin.eplin.violators.index')); ?>" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="<?php echo e($search); ?>" placeholder="Cari nama atau NIS siswa"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    <?php if($search): ?>
                        <a href="<?php echo e(route('admin.eplin.violators.index')); ?>" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">NAMA SISWA</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">JUMLAH PELANGGARAN</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">PELANGGARAN TERAKHIR</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-900 dark:text-slate-100">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        <?php $__empty_1 = true; $__currentLoopData = $violators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $violator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100"><?php echo e($violator->name); ?></td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400"><?php echo e($violator->student_number); ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">
                                        <?php echo e($violator->violations_count); ?> Pelanggaran
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <?php if($violator->violations->first()): ?>
                                        <?php echo e($violator->violations->first()->violation_date->format('d M Y')); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?php echo e(route('admin.eplin.violators.show', $violator->id)); ?>"
                                           class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Lihat Detail
                                        </a>
                                        <button type="button"
                                                onclick="deleteViolator('<?php echo e($violator->id); ?>', '<?php echo e($violator->name); ?>')"
                                                class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-solid fa-inbox text-3xl text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data pelanggar.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php echo e($violators->links()); ?>

    </div>

    <script>
        function deleteViolator(violatorId, violatorName) {
            Swal.fire({
                title: 'Hapus Pelanggar',
                text: `Pilih metode penghapusan untuk ${violatorName}:`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus (Backup)',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: (modal) => {
                    const confirmBtn = modal.querySelector('.swal2-confirm');
                    const cancelBtn = modal.querySelector('.swal2-cancel');

                    // Tambah tombol Hapus Permanen
                    const permanentBtn = document.createElement('button');
                    permanentBtn.className = 'swal2-confirm swal2-styled mx-2 bg-red-600';
                    permanentBtn.textContent = 'Hapus Permanen';
                    permanentBtn.style.display = 'none';
                    modal.querySelector('.swal2-actions').insertBefore(permanentBtn, confirmBtn.nextSibling);

                    confirmBtn.onclick = () => submitDelete(violatorId, 'soft');
                    permanentBtn.onclick = () => submitDelete(violatorId, 'permanent');
                }
            });
        }

        function submitDelete(violatorId, type) {
            const url = type === 'permanent'
                ? `/admin/eplin/violator-students/${violatorId}/force`
                : `/admin/eplin/violator-students/${violatorId}`;

            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                text: type === 'permanent'
                    ? 'Data pelanggar dan semua pelanggaran akan dihapus secara permanen dan tidak dapat dipulihkan. Lanjutkan?'
                    : 'Data pelanggar dan semua pelanggaran akan disimpan dalam backup. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
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
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/eplin/violators/index.blade.php ENDPATH**/ ?>