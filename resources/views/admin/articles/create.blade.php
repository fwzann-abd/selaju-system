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
                    <textarea id="article-editor" name="content" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500" rows="8">{{ old('content') }}</textarea>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('admin.articles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
                    <button type="submit" class="ml-3 rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Create</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css">
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/suneditor@latest/src/lang/en.js"></script>

    <style>
    .sun-editor {
        border-radius: .75rem;
        overflow: hidden;
    }

    .dark .sun-editor .se-toolbar,
    .dark .sun-editor .se-toolbar-more-layer,
    .dark .sun-editor .se-btn-module-border {
        background-color: #111b2e;
        color: #f1f5f9;
        border-color: #1f2b44;
    }

    .dark .sun-editor .se-wrapper-inner {
        background-color: #0f172a;
        color: #f8fafc;
    }
    </style>

    <script>
        function initSunEditor(editorId = 'article-editor') {
            if (typeof SUNEDITOR === 'undefined') {
                return setTimeout(function () { initSunEditor(editorId); }, 100);
            }

            const textarea = document.getElementById(editorId);
            if (!textarea) return;

            if (textarea._sunEditorInstance) {
                textarea._sunEditorInstance.destroy();
            }

            const editor = SUNEDITOR.create(textarea, {
                lang: SUNEDITOR_LANG['en'],
                height: 420,
                width: '100%',
                minHeight: '420px',
                buttonList: [
                    ['undo', 'redo'],
                    ['font', 'fontSize', 'formatBlock'],
                    ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript', 'removeFormat'],
                    ['fontColor', 'hiliteColor'],
                    ['outdent', 'indent', 'align', 'list', 'lineHeight'],
                    ['link', 'image', 'video', 'audio', 'table', 'codeView'],
                    ['fullScreen', 'showBlocks', 'preview', 'print']
                ],
                imageUploadUrl: '',
            });

            textarea._sunEditorInstance = editor;

            const form = textarea.closest('form');
            if (form && form.dataset.suneditorSync !== 'true') {
                form.addEventListener('submit', function () {
                    textarea.value = editor.getContents(true);
                });
                form.dataset.suneditorSync = 'true';
            }
        }

        function runWhenReady(callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback, { once: true });
            } else {
                callback();
            }
        }

        runWhenReady(function () {
            initSunEditor('article-editor');
        });
    </script>
    @endpush
</x-app-layout>
