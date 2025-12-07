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
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    @if($shop->photo)
                        <img src="{{ asset('storage/assets/modules/sejajan/mart/' . $shop->photo) }}"
                             alt="{{ $shop->name }}"
                             class="w-full rounded-2xl object-cover">
                    @else
                        <div class="flex h-64 w-full items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                            <i class="fa-solid fa-store text-6xl text-slate-400"></i>
                        </div>
                    @endif
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nama Toko</h3>
                        <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $shop->name }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Slug</h3>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $shop->slug }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Pemilik</h3>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $shop->participant?->name ?? '-' }}</p>
                        <p class="text-sm text-slate-500">{{ $shop->participant?->email }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Deskripsi</h3>
                        <p class="mt-1 text-slate-700 dark:text-slate-200">{{ $shop->description ?? '-' }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Status</h3>
                        <div class="mt-1">
                            @if($shop->is_active)
                                <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Dibuat</h3>
                        <p class="mt-1 text-sm text-slate-700 dark:text-slate-200">{{ $shop->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                <a href="{{ route('admin.sejajan.index') }}"
                   class="rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Kembali
                </a>
                <a href="{{ route('admin.sejajan.edit', $shop) }}"
                   class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Edit Toko
                </a>
            </div>
        </div>

        @if($shop->products && $shop->products->count() > 0)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="mb-4 text-lg font-semibold text-slate-900 dark:text-white">Daftar Produk ({{ $shop->products->count() }})</h3>

                <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                            <thead class="bg-slate-50 dark:bg-slate-800/60">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Produk</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Harga</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Stok</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                                @foreach($shop->products as $product)
                                    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                        <td class="px-6 py-4 text-sm">
                                            <div class="flex items-center gap-3">
                                                @if($product->photo)
                                                    <img src="{{ $photoPath . $product->photo }}"
                                                         alt="{{ $product->name }}"
                                                         class="h-12 w-12 rounded-lg object-cover">
                                                @else
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800">
                                                        <i class="fa-solid fa-box text-slate-400"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-semibold text-slate-800 dark:text-slate-100">{{ $product->name }}</div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $product->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-700 dark:text-slate-300">
                                            Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                            {{ $product->stock }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($product->is_active)
                                                <span class="inline-block rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-200">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                                                    Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <i class="fa-solid fa-box-open text-4xl text-slate-300 dark:text-slate-700"></i>
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Belum ada produk di toko ini</p>
            </div>
        @endif
    </div>
</x-app-layout>
