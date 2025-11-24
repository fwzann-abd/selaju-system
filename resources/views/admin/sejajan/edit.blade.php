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
        <form action="{{ route('admin.sejajan.update', $shop) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="participant_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Pemilik Toko <span class="text-red-500">*</span></label>
                    <select id="participant_id" name="participant_id" required
                            class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Peserta --</option>
                        @foreach($participants as $participant)
                            <option value="{{ $participant->id }}" {{ old('participant_id', $shop->participant_id) == $participant->id ? 'selected' : '' }}>
                                {{ $participant->name }} ({{ $participant->username }})
                            </option>
                        @endforeach
                    </select>
                    @error('participant_id')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama Toko <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $shop->name) }}" required
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           placeholder="Toko Makanan Sehat">
                    @error('name')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $shop->slug) }}"
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           placeholder="toko-makanan-sehat">
                    @error('slug')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Kosongkan untuk auto-generate dari nama toko</p>
                </div>

                <div>
                    <label for="photo" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Foto Toko</label>
                    @if($shop->photo)
                        <div class="mb-2 mt-2">
                            <img src="{{ asset('storage/assets/modules/sejajan/mart/' . $shop->photo) }}"
                                 alt="{{ $shop->name }}"
                                 class="h-32 w-32 rounded-xl object-cover">
                        </div>
                    @endif
                    <input id="photo" name="photo" type="file" accept="image/*"
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100 focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    @error('photo')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-slate-500">Max 2MB, format: jpeg, png, jpg, gif. Kosongkan jika tidak ingin mengganti foto</p>
                </div>
            </div>

            <div>
                <label for="description" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                          class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:dark:bg-slate-800 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                          placeholder="Deskripsi singkat tentang toko">{{ old('description', $shop->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $shop->is_active) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-slate-300 text-indigo-600 transition focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-600 dark:bg-slate-800">
                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-200">Toko Aktif</span>
                </label>
            </div>

            <div class="flex gap-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                <a href="{{ route('admin.sejajan.index') }}"
                   class="rounded-xl border border-slate-200 px-6 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    Update Toko
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
