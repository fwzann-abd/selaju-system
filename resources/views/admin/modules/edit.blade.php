<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @foreach($breadcrumb as $key => $item)
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
        <form action="{{ route('admin.modules.update', $module) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Module Information -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Informasi Module</h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="menu_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Menu</label>
                        <select name="menu_id" id="menu_id" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Pilih Menu</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ old('menu_id', $module->menu_id) == $menu->id ? 'selected' : '' }}>
                                    {{ $menu->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('menu_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="identifiers" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Identifiers</label>
                        <input type="text" name="identifiers" id="identifiers" value="{{ old('identifiers', $module->identifiers) }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500"
                               placeholder="contoh: admin.users.index">
                        @error('identifiers')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Module</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $module->name) }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="url" class="block text-sm font-medium text-slate-700 dark:text-slate-300">URL</label>
                        <input type="text" name="url" id="url" value="{{ old('url', $module->url) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500"
                               placeholder="/admin/users atau https://example.com">
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Icon</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $module->icon) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500"
                               placeholder="fa-home, fa-users, dll">
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="row_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Urutan</label>
                        <input type="number" name="row_order" id="row_order" value="{{ old('row_order', $module->row_order) }}" min="1" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder-slate-500">
                        @error('row_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="is_active" id="active" value="1"
                                       {{ old('is_active', $module->is_active) == '1' ? 'checked' : '' }}
                                       class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-600 dark:border-slate-700">
                                <label for="active" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Aktif</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="is_active" id="inactive" value="0"
                                       {{ old('is_active', $module->is_active) == '0' ? 'checked' : '' }}
                                       class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-600 dark:border-slate-700">
                                <label for="inactive" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Tidak Aktif</label>
                            </div>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.modules.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Perbarui Module
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
