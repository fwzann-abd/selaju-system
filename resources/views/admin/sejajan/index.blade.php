<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @foreach($breadcrumb as $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if($item['url'])
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-300">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                        <span class="mx-2">/</span>
                    @endif
                @endforeach
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle }}</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="{{ route('admin.sejajan.index') }}" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama toko atau pemilik"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    @if($search)
                        <a href="{{ route('admin.sejajan.index') }}" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.sejajan.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Toko
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Toko</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pemilik</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Deskripsi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Produk</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @forelse($shops as $shop)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center gap-3">
                                        @if($shop->photo)
                                            <img src="{{ asset('storage/assets/modules/sejajan/mart/' . $shop->photo) }}"
                                                 alt="{{ $shop->name }}"
                                                 class="h-12 w-12 rounded-lg object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800">
                                                <i class="fa-solid fa-store text-slate-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $shop->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $shop->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ $shop->participant?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    {{ Str::limit($shop->description ?? '-', 50) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-200">
                                        <i class="fa-solid fa-box text-[10px]"></i>
                                        {{ $shop->products_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($shop->is_active)
                                        <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.sejajan.show', $shop) }}"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.sejajan.edit', $shop) }}"
                                           class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.sejajan.destroy', $shop) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus toko ini? Semua produk di toko ini juga akan dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-200">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data toko sejajan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($shops->hasPages())
            <div class="flex justify-center">
                {{ $shops->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
