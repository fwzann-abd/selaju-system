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
        <form action="<?php echo e(route('admin.schools.store')); ?>" method="POST"
              x-data="(function(){ return {
                  name: <?php echo \Illuminate\Support\Js::from(old('name', ''))->toHtml() ?>,
                  slug: <?php echo \Illuminate\Support\Js::from(old('slug', ''))->toHtml() ?>,
                  slugTouched: (<?php echo \Illuminate\Support\Js::from(old('slug', ''))->toHtml() ?> ? true : false),
                  init() {
                      if (!this.slug) this.slug = this.slugify(this.name)
                  },
                  slugify(value) {
                      return (value ?? '').toString().toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '')
                  },
                  handleNameInput(e) {
                      this.name = e?.target?.value ?? this.name
                      if (!this.slugTouched || !this.slug) {
                          this.slug = this.slugify(this.name)
                      }
                  },
                  handleSlugInput(e) {
                      this.slug = this.slugify(e?.target?.value ?? '')
                      this.slugTouched = true
                  }
              } })()"
              class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama Sekolah</label>
                    <input id="name" name="name" type="text" x-model="name" @input="handleNameInput($event)" autocomplete="off"
                  required
                  class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           placeholder="SMKN 1 Selaju">
                    <?php $__errorArgs = ['name'];
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
                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Slug</label>
                  <input id="slug" name="slug" type="text" x-bind:value="slug" readonly aria-readonly="true" autocomplete="off"
                  required
                  class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white cursor-not-allowed"
                           placeholder="smkn-1-selaju">
                    <p class="mt-1 text-xs text-slate-400">Slug otomatis mengikuti nama, dan dapat disesuaikan.</p>
                    <?php $__errorArgs = ['slug'];
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
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?php echo e(route('admin.schools.index')); ?>"
                   class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-2xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700">
                    Simpan Sekolah
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

<?php if (! $__env->hasRenderedOnce('27e46afe-ee3d-4898-b93c-b06d91dfabd9')): $__env->markAsRenderedOnce('27e46afe-ee3d-4898-b93c-b06d91dfabd9'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('schoolForm', ({ initialName = '', initialSlug = '' } = {}) => ({
                    // ensure initial values are strings; guard against unexpected objects
                    name: (typeof initialName === 'string' ? initialName : ''),
                    slug: (typeof initialSlug === 'string' ? initialSlug : ''),
                    slugTouched: (typeof initialSlug === 'string' ? initialSlug.length > 0 : false),
                    init() {
                        if (!this.slug) {
                            this.slug = this.slugify(this.name)
                        }
                    },
                    slugify(value) {
                        return (value ?? '')
                            .toString()
                            .toLowerCase()
                            .trim()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '')
                    },
                    handleNameInput(e) {
                        // prefer the event value to avoid accidental element assignment
                        this.name = e?.target?.value ?? this.name
                        if (!this.slugTouched || !this.slug) {
                            this.slug = this.slugify(this.name)
                        }
                    },
                    handleSlugInput(e) {
                        // use the input event value to avoid accidentally assigning the element object
                        this.slug = this.slugify(e?.target?.value ?? '')
                        this.slugTouched = true
                    }
                }))
            })
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\LMSBACKEND\selaju-system\resources\views/admin/schools/create.blade.php ENDPATH**/ ?>