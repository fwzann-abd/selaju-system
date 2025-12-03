<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.perpossagar-categories.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Kategori Buku</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Edit Kategori</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Edit Kategori Buku</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <form action="{{ route('admin.perpossagar-categories.update', $category->uuid) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="name" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    placeholder="Contoh: Fiksi, Non-Fiksi, Komik, dll"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900 @error('name') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                />
                @error('name')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.perpossagar-categories.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
