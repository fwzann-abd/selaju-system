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

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form x-data="studentImportComponent({ initialSchool: <?php echo \Illuminate\Support\Js::from(old('school_id'))->toHtml() ?>, previewUrl: <?php echo \Illuminate\Support\Js::from(route('admin.students.import.preview'))->toHtml() ?> })" x-cloak
            action="<?php echo e(route('admin.students.import')); ?>" method="POST" enctype="multipart/form-data"
            @submit="onSubmit($event)" class="space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="import_token" :value="importToken">

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="generation_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Generasi Saat Ini</label>
                    <input type="hidden" name="generation_id" value="<?php echo e($currentGeneration?->id); ?>">
                    <div class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <?php echo e($currentGeneration?->name ?? 'Tidak ada generasi yang aktif'); ?>

                    </div>
                </div>

                <div>
                    <label for="school_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Sekolah</label>
                    <select id="school_id" name="school_id" required x-model="schoolId"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Sekolah --</option>
                        <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sch->id); ?>" <?php if(old('school_id') == $sch->id): ?> selected <?php endif; ?>><?php echo e($sch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['school_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-[#EF4444]"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div x-data="{}" class="">
                    <label class="text-sm font-semibold text-slate-600 dark:text-slate-200">File Excel (.xlsx)</label>

                    <div class="mt-3 relative">
                        <label for="file" class="relative flex h-16 w-full cursor-pointer items-center justify-between gap-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 hover:border-indigo-400 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                            <div class="flex items-center gap-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <div>
                                    <div class="text-sm font-medium">Pilih atau seret file Excel di sini</div>
                                    <div class="text-xs text-slate-400" x-text="filename ? filename : 'Gunakan template yang disediakan. Kolom: name, student_number, national_id, gender (L/P).'">
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-500">.xlsx</span>
                                <button type="button" class="rounded-md bg-white/0 px-3 py-1 text-sm text-slate-700">Pilih</button>
                            </div>

                            <input id="file" x-ref="fileInput" name="file" type="file" accept=".xlsx,.xls" required @change="onFileChange($event)" class="absolute inset-0 h-full w-full opacity-0 cursor-pointer" />
                        </label>

                        <div class="pointer-events-none absolute inset-0 flex items-center justify-center rounded-2xl bg-white/70 text-sm font-medium text-slate-600 dark:bg-slate-900/70 dark:text-slate-200" x-show="isPreviewing" x-transition.opacity>
                            <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            Memproses file...
                        </div>

                        <div class="mt-2 text-xs text-slate-500">File maksimum 5MB. Hanya .xlsx/.xls.</div>
                    </div>

                    <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-[#EF4444]"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500 dark:text-slate-400" x-show="importToken">
                            Data siap diunggah. Silakan periksa preview sebelum menekan Upload.
                        </div>
                        <button type="button" class="inline-flex items-center rounded-2xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800" @click="startPreview" :disabled="isPreviewing">
                            <svg x-show="isPreviewing" x-cloak class="-ml-1 mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="isPreviewing ? 'Memproses file...' : 'Import (Preview)'">Import (Preview)</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <p class="text-sm font-semibold text-slate-600 dark:text-slate-200 mb-3">Format File:</p>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-slate-100 dark:bg-slate-800">
                                <th class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-200">No</th>
                                <th class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-200">name</th>
                                <th class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-200">student_number</th>
                                <th class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-200">national_id</th>
                                <th class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs font-semibold text-slate-600 dark:text-slate-200">gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-600 dark:text-slate-300">1</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs text-slate-600 dark:text-slate-300">Contoh Nama Siswa</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-600 dark:text-slate-300">12345678</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-600 dark:text-slate-300">0012345678</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-600 dark:text-slate-300">L</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-400 dark:text-slate-500">2</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-xs text-slate-400 dark:text-slate-500">...</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-400 dark:text-slate-500">...</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-400 dark:text-slate-500">...</td>
                                <td class="border border-slate-200 dark:border-slate-700 px-4 py-2 text-center text-xs text-slate-400 dark:text-slate-500">...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-200">Preview Data</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400" x-show="!previewRows.length">
                            Klik tombol Import (Preview) untuk melihat isi file sebelum disimpan.
                        </p>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400" x-show="summary.valid || summary.invalid" x-cloak>
                        <span class="mr-4">Valid: <span class="font-semibold" x-text="summary.valid"></span></span>
                        <span>Invalid: <span class="font-semibold" x-text="summary.invalid"></span></span>
                    </div>
                </div>

                <div x-show="previewErrors.length" x-cloak
                    class="rounded-2xl border border-amber-300 bg-amber-50/70 p-4 text-xs text-amber-800 dark:border-amber-600 dark:bg-amber-500/10 dark:text-amber-200">
                    <p class="font-semibold text-sm">Baris bermasalah</p>
                    <ul class="mt-2 list-disc space-y-1 pl-4">
                        <template x-for="(error, index) in previewErrors.slice(0, 5)" :key="`err-${index}`">
                            <li x-text="error"></li>
                        </template>
                    </ul>
                    <p class="mt-2" x-show="previewErrors.length > 5">
                        Dan <span x-text="previewErrors.length - 5"></span> catatan lainnya.
                    </p>
                </div>

                <div x-show="previewRows.length" x-cloak class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                        <span x-text="`Menampilkan ${previewRows.length ? ((previewPage - 1) * perPage + 1) : 0}-${Math.min(previewPage * perPage, previewRows.length)} dari ${previewRows.length} data`"></span>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="prevPage" :disabled="previewPage === 1"
                                class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:hover:bg-slate-800">
                                Sebelumnya
                            </button>
                            <span class="text-slate-600 dark:text-slate-300" x-text="previewPage + ' / ' + totalPages"></span>
                            <button type="button" @click="nextPage" :disabled="previewPage === totalPages"
                                class="rounded-full border border-slate-200 px-3 py-1 text-xs font-medium hover:bg-slate-50 disabled:opacity-50 dark:border-slate-700 dark:hover:bg-slate-800">
                                Selanjutnya
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-200">
                                    <th class="border border-slate-200 px-3 py-2 text-left dark:border-slate-700">No</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left dark:border-slate-700">name</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left dark:border-slate-700">student_number</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left dark:border-slate-700">national_id</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left dark:border-slate-700">gender</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in paginatedRows" :key="row.row_number ?? index">
                                    <tr class="odd:bg-white even:bg-slate-50 text-slate-700 dark:odd:bg-slate-900 dark:even:bg-slate-800 dark:text-slate-200">
                                        <td class="border border-slate-200 px-3 py-2 text-center dark:border-slate-800" x-text="row.row_number ?? ((previewPage - 1) * perPage + index + 1)"></td>
                                        <td class="border border-slate-200 px-3 py-2 dark:border-slate-800" x-text="row.name"></td>
                                        <td class="border border-slate-200 px-3 py-2 dark:border-slate-800" x-text="row.student_number || '-' "></td>
                                        <td class="border border-slate-200 px-3 py-2 dark:border-slate-800" x-text="row.national_id"></td>
                                        <td class="border border-slate-200 px-3 py-2 uppercase dark:border-slate-800" x-text="row.gender"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3">
                <a href="<?php echo e(route('admin.students.index')); ?>" class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
                <button type="submit" :disabled="isUploading || !importToken" class="relative inline-flex items-center rounded-2xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                    <svg x-show="isUploading" x-cloak class="-ml-1 mr-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span x-text="isUploading ? 'Mengunggah...' : 'Upload & Import'">Upload & Import</span>
                </button>
            </div>
        </form>
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
<?php /**PATH D:\shei\folder-v2\resources\views/admin/students/import.blade.php ENDPATH**/ ?>