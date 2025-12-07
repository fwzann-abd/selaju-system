<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span>Perpossagar</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Kategori Buku</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Kategori Buku</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Actions Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Total {{ method_exists($categories, 'total') ? $categories->total() : (is_countable($categories) ? count($categories) : 0) }} kategori
                </p>
            </div>
            <a href="{{ route('admin.perpossagar-categories.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Kategori
            </a>
        </div>

        <!-- Status Messages -->
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-sm text-red-500"></i>
                    <div class="text-sm text-red-600 dark:text-red-400">
                        <p class="font-medium">Ada beberapa kesalahan:</p>
                        <ul class="mt-1 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-sm text-green-500"></i>
                    <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Categories Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Dibuat</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @forelse($categories as $category)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ isset($category->created_at) ? \Illuminate\Support\Carbon::parse($category->created_at)->format('d M Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.perpossagar-categories.edit', $category->uuid) }}"
                                       class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                        <i class="fa-solid fa-pencil text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.perpossagar-categories.destroy', $category->uuid) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center">
                                <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada kategori</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(method_exists($categories, 'hasPages') && $categories->hasPages())
        <div class="flex justify-center">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
