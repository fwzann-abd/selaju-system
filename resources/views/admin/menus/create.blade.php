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
        <form action="{{ route('admin.menus.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Menu</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Icon</label>
                        <div class="flex items-center gap-3">
                            <input type="text" name="icon" id="icon" value="{{ old('icon') }}" required
                                   class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                   placeholder="Click to choose icon" data-is-iconpicker="true" data-preview="#icon-preview">
                            <span class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm">
                                <i id="icon-preview" class="fa-solid {{ old('icon') ?? 'fa-cube' }} text-xl"></i>
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Click the input field to open the icon picker with hundreds of FontAwesome icons.</p>
                        @error('icon')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="row_order" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Urutan</label>
                        <input type="number" name="row_order" id="row_order" value="{{ old('row_order', 1) }}" min="1" required
                               class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        @error('row_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center">
                                <input type="radio" name="status" id="active" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }} class="h-4 w-4">
                                <label for="active" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Aktif</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="status" id="inactive" value="0" {{ old('status') == '0' ? 'checked' : '' }} class="h-4 w-4">
                                <label for="inactive" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Tidak Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.menus.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm">Batal</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Simpan Menu</button>
            </div>
        </form>
    </div>
    @include('components.icon-picker')
</x-app-layout>
