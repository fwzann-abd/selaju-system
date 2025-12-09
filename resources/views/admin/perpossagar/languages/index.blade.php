<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span>Perpossagar</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Bahasa Buku</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Bahasa Buku</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola daftar bahasa yang bisa dipilih penulis.</p>
            <a href="{{ route('admin.perpossagar-book-langs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Bahasa
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Slug</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($languages as $language)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $language->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $language->slug }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.perpossagar-book-langs.edit', $language->uuid) }}" class="rounded-lg px-3 py-1 text-sm text-indigo-600 hover:bg-indigo-50">Edit</a>
                                <form action="{{ route('admin.perpossagar-book-langs.destroy', $language->uuid) }}" method="POST" class="inline" onsubmit="return confirm('Hapus bahasa ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-6 text-center text-sm text-slate-500">Belum ada bahasa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $languages->links() }}
    </div>
</x-app-layout>
