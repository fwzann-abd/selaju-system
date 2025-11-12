<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.article-categories.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Article Categories</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Create</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Create Article Category</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900" />
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ route('admin.article-categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">Cancel</a>
                        <button type="submit" class="ml-3 rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </x-app-layout>
