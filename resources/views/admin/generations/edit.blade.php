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

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="{{ route('admin.generations.update', $generation) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama Generasi</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $generation->name) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                           placeholder="Contoh: Generasi Z" required>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_years" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Tahun Mulai</label>
                    <input type="number" id="start_years" name="start_years" value="{{ old('start_years', $generation->start_years) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                           placeholder="Contoh: 1997" required>
                    @error('start_years')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_years" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Tahun Akhir</label>
                    <input type="number" id="end_years" name="end_years" value="{{ old('end_years', $generation->end_years) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                           placeholder="Contoh: 2012" required>
                    @error('end_years')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/30">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $generation->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600">
                    <div>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">Jadikan Generasi Aktif</span>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Peserta yang mendaftar akan otomatis masuk ke generasi ini</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-check text-xs"></i>
                    Perbarui Generasi
                </button>
                <a href="{{ route('admin.generations.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
