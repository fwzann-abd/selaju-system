<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @foreach($breadcrumb as $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if($item['url'])
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-200">{{ $item['label'] }}</a>
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
            <div class="rounded-xl border border-emerald-300/30 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.schools.config.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-[28px] border border-slate-800 bg-slate-900 p-6 text-white shadow-[0_25px_60px_-30px_rgba(15,23,42,0.7)]">
                <h3 class="mb-6 text-lg font-semibold">Informasi Module</h3>

                @php
                    $selectedMenu = old('menu_id', $module->menu_id ?? ($menus->first()->id ?? ''));
                @endphp

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="menu_id" class="block text-sm font-medium text-slate-300">Menu</label>
                        <select
                            id="menu_id"
                            name="menu_id"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-400 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                        >
                            <option value="">Pilih Menu</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ (string)$selectedMenu === (string)$menu->id ? 'selected' : '' }}>
                                    {{ $menu->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('menu_id')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="identifiers" class="block text-sm font-medium text-slate-300">Identifiers</label>
                        <input
                            type="text"
                            id="identifiers"
                            name="identifiers"
                            value="{{ old('identifiers', $module->identifiers) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                            placeholder="schools"
                        >
                        @error('identifiers')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300">Nama Module</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $module->name) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                            placeholder="School"
                        >
                        @error('name')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="url" class="block text-sm font-medium text-slate-300">URL</label>
                        <input
                            type="text"
                            id="url"
                            name="url"
                            value="{{ old('url', $module->url) }}"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                            placeholder="/admin/schools"
                        >
                        @error('url')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-slate-300">Icon</label>
                        <input
                            type="text"
                            id="icon"
                            name="icon"
                            value="{{ old('icon', $module->icon) }}"
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                            placeholder="fa-home, fa-users, dll"
                        >
                        @error('icon')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="row_order" class="block text-sm font-medium text-slate-300">Urutan</label>
                        <input
                            type="number"
                            id="row_order"
                            name="row_order"
                            min="1"
                            value="{{ old('row_order', $module->row_order ?? 1) }}"
                            required
                            class="mt-2 w-full rounded-xl border border-white/10 bg-slate-800/70 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-sky-400 focus:outline-none focus:ring focus:ring-sky-500/50"
                        >
                        @error('row_order')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <span class="block text-sm font-medium text-slate-300">Status</span>
                        <div class="mt-3 flex flex-col gap-3 text-sm">
                            @php
                                $status = old('is_active', ($module->is_active ?? true) ? '1' : '0');
                            @endphp
                            <label class="flex items-center gap-3">
                                <input type="radio" name="is_active" value="1" class="h-4 w-4 border-slate-600 text-sky-400 focus:ring-sky-500" {{ $status === '1' ? 'checked' : '' }}>
                                <span>Aktif</span>
                            </label>
                            <label class="flex items-center gap-3">
                                <input type="radio" name="is_active" value="0" class="h-4 w-4 border-slate-600 text-sky-400 focus:ring-sky-500" {{ $status === '0' ? 'checked' : '' }}>
                                <span>Tidak Aktif</span>
                            </label>
                            @error('is_active')
                                <p class="text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-xl border border-white/20 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-white/5"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-xl bg-sky-500 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-sky-500/40 hover:bg-sky-400"
                >
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
