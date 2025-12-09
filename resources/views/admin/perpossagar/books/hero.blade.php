<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span>Perpossagar</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Setting Hero</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Atur Buku Hero</h2>
        </div>
    </x-slot>

    @php
        $oldHeroIds = old('hero_books', $books->where('is_hero', true)->pluck('uuid')->toArray());
        $oldHeroOrders = old('hero_orders', []);
    @endphp

    <div class="space-y-6">
        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 text-sm text-slate-700 dark:border-indigo-500/30 dark:bg-indigo-900/20 dark:text-slate-200">
            <p class="font-semibold text-slate-900 dark:text-white">Petunjuk</p>
            <ul class="mt-2 list-disc pl-5 text-slate-600 dark:text-slate-300">
                <li>Pilih maksimal 5 buku yang ingin ditampilkan di hero aplikasi Perpossagar.</li>
                <li>Gunakan kolom urutan untuk menentukan posisi (1 = paling atas).</li>
                <li>Perubahan akan langsung diterapkan setelah menekan tombol simpan.</li>
            </ul>
        </div>

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm font-semibold text-red-600 dark:text-red-300">Terjadi kesalahan validasi:</p>
                <ul class="mt-2 list-disc pl-5 text-sm text-red-500 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.perpossagar-books.hero.update') }}" class="space-y-5">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($books as $book)
                    <label class="flex cursor-pointer flex-col gap-3 rounded-2xl border px-4 py-4 shadow-sm transition hover:border-indigo-300 dark:border-slate-800 dark:bg-slate-900/60 @if($book->is_hero) border-indigo-400 bg-indigo-50/70 dark:border-indigo-500/50 dark:bg-indigo-900/40 @endif">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-base font-semibold text-slate-900 dark:text-white">{{ $book->title }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-300">{{ $book->author_display_name ?? '—' }}</p>
                            </div>
                            <input
                                type="checkbox"
                                name="hero_books[]"
                                value="{{ $book->uuid }}"
                                class="mt-1 h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                @checked(in_array($book->uuid, $oldHeroIds))
                            />
                        </div>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($book->categories as $category)
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $category->name }}</span>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <label class="text-slate-500 dark:text-slate-300">Urutan</label>
                            <input
                                type="number"
                                min="1"
                                max="20"
                                name="hero_orders[{{ $book->uuid }}]"
                                value="{{ $oldHeroOrders[$book->uuid] ?? $book->hero_order }}"
                                class="w-20 rounded-xl border border-slate-200 px-3 py-1 text-center text-slate-700 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            />
                        </div>
                        @if($book->is_hero)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-300">
                                <i class="fa-solid fa-star"></i>
                                Saat ini tampil di hero
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-300">
                <span>{{ $currentHeroes }} / 5 buku hero aktif</span>
                <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
