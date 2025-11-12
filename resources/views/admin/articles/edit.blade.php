<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @php $breadcrumb = $breadcrumb ?? [ ['label' => 'Articles', 'url' => route('admin.articles.index')], ['label' => 'Edit', 'url' => null] ]; @endphp
                @foreach($breadcrumb as $key => $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if(!empty($item['url']))
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-300">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                        <span class="mx-2">/</span>
                    @endif
                @endforeach
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Edit Article</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <form action="#" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Category</label>
                    <select name="category_id" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @if(!empty($categories) && $categories->count())
                            <option value="">-- Choose --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ (old('category_id') ?? ($article->category_id ?? '')) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        @else
                            <option disabled value="">No categories available</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                    <input type="text" name="title" value="{{ old('title', $article->title ?? 'Example Article') }}" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Content</label>
                    <textarea id="tinymce-editor" name="content" rows="8" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">{{ old('content', $article->content ?? 'Example content') }}</textarea>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('admin.articles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
                    <button type="submit" class="ml-3 rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Save</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
    {{-- TinyMCE loaded from local npm package --}}
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>

    <style>
    /* TinyMCE container styling to match dark mode */
    .tox-tinymce { border-radius: .5rem; }
    .dark .tox-tinymce { background-color: #0f172a; color: #fff; }
    </style>

    <script>
        function initTinyMCE(editorId = 'tinymce-editor') {
            if (typeof tinymce === 'undefined') {
                return setTimeout(function () { initTinyMCE(editorId); }, 100);
            }

            const existing = tinymce.get(editorId);
            if (existing) existing.remove();

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
                    const reader = new FileReader();
                    reader.onload = function () { success(reader.result); };
                    reader.readAsDataURL(blobInfo.blob());
                }
            });

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
