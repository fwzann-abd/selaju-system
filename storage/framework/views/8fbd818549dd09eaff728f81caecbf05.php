<div class="space-y-6">
    <?php if($errors->any()): ?>
        <div class="rounded-xl border border-rose-300/40 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            <ul class="list-inside list-disc">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="<?php echo e($action); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php if($method === 'PUT'): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="school_id">Sekolah</label>
                    <select id="school_id" name="school_id" required
                            class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Pilih sekolah</option>
                        <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schoolOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($schoolOption->id); ?>"
                                <?php if(old('school_id', $teacher?->school_id) == $schoolOption->id): echo 'selected'; endif; ?>>
                                <?php echo e($schoolOption->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="name">Nama Guru</label>
                    <input id="name" name="name" type="text" value="<?php echo e(old('name', $teacher?->name)); ?>" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="nip">NIP</label>
                    <input id="nip" name="nip" type="text" value="<?php echo e(old('nip', $teacher?->nip)); ?>"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="username">Username</label>
                    <input id="username" name="username" type="text" value="<?php echo e(old('username', $teacher?->account->username ?? '')); ?>" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?php echo e(old('email', $teacher?->account->email ?? '')); ?>" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="password">Password</label>
                    <input id="password" name="password" type="password" <?php if($method !== 'PUT'): ?> required <?php endif; ?>
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                    <?php if($method === 'PUT'): ?>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Biarkan kosong jika tidak ingin mengubah password.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
                <a href="<?php echo e(route('admin.teachers.index')); ?>"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <?php echo e($buttonLabel); ?>

                </button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/teachers/_form.blade.php ENDPATH**/ ?>