<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.articles.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Articles</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Create</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Create Article</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="{{ route('admin.articles.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
                    <select name="category_id" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @if(!empty($categories) && $categories->count())
                            <option value="">-- Select category --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        @else
                            <option disabled value="">No categories available</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Content</label>
                    <textarea id="tinymce-editor" name="content" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500" rows="8">{{ old('content') }}</textarea>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('admin.articles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
                    <button type="submit" class="ml-3 rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    {{-- TinyMCE from cdnjs (kept in sync with system-nempogarut) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>

    <style>
    /* TinyMCE container styling to match dark mode */
    .tox-tinymce { border-radius: .5rem; }
    .dark .tox-tinymce { background-color: #0f172a; color: #fff; }
    </style>

    <script>
        function initTinyMCE(editorId = 'tinymce-editor') {
            if (typeof tinymce === 'undefined') {
                // try again shortly if tinymce isn't loaded yet
                return setTimeout(function () { initTinyMCE(editorId); }, 100);
            }

            // remove existing editor instance if present to avoid duplicate init
            const existing = tinymce.get(editorId);
            if (existing) {
                existing.remove();
            }

            const dark = document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');

            tinymce.init({
                selector: '#' + editorId,
                height: 420,
                menubar: false,
                plugins: 'lists link image media table code advlist autolink charmap preview paste',
                toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code',
                skin: dark ? 'oxide-dark' : 'oxide',
                content_css: dark ? 'dark' : 'default',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                images_upload_handler: function (blobInfo, success, failure) {
                    // current fallback: inline base64
                    const reader = new FileReader();
                    reader.onload = function () { success(reader.result); };
                    reader.readAsDataURL(blobInfo.blob());
                }
            });

            // ensure form submission syncs editor content to textarea
            const textarea = document.getElementById(editorId);
            if (textarea) {
                const form = textarea.closest('form');
                if (form && form.dataset.tinymceSync !== 'true') {
                    form.addEventListener('submit', function () {
                        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
                    });
                    form.dataset.tinymceSync = 'true';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initTinyMCE('tinymce-editor');
        });
    </script>
@endpush
                                </div>
