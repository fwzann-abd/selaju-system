<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span>Perpossagar</span>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.perpossagar-book-langs.index') }}" class="text-indigo-600">Bahasa Buku</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Tambah Bahasa</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Tambah Bahasa Buku</h2>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('admin.perpossagar-book-langs.store') }}" class="space-y-5 max-w-xl">
        @csrf
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3" required />
            @error('name')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700">Slug</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3" required />
            <p class="text-xs text-slate-400">Contoh: id, en, sundanese.</p>
            @error('slug')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    </form>
</x-app-layout>
