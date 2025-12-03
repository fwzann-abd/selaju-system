<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.perpossagar-books.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Daftar Buku</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Tambah Buku</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Tambah Buku Baru</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <form action="{{ route('admin.perpossagar-books.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <!-- Categories Field (REQUIRED) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="categories" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select
                    id="categories"
                    name="categories[]"
                    multiple
                    required
                    class="w-full"
                    data-placeholder="Pilih satu atau lebih kategori..."
                >
                    @foreach($categories as $category)
                        <option value="{{ $category->uuid }}" {{ in_array($category->uuid, old('categories', [])) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('categories')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
                @if($categories->isEmpty())
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">
                        Belum ada kategori. <a href="{{ route('admin.perpossagar-categories.create') }}" class="font-medium hover:underline">Buat kategori terlebih dahulu</a>
                    </p>
                @endif
            </div>

            @push('scripts')
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css">
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
                <style>
                    .ts-wrapper {
                        width: 100%;
                    }
                    .ts-control {
                        border: 1px solid rgb(203, 213, 225);
                        border-radius: 0.75rem;
                        background-color: white;
                        padding: 0.5rem 0.25rem;
                        transition: border-color 0.15s, box-shadow 0.15s;
                        font-size: 1rem;
                    }
                    .ts-control:focus-within {
                        border-color: rgb(99, 102, 241);
                        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
                    }
                    .ts-control.has-items {
                        padding: 0.25rem;
                    }
                    .ts-wrapper.multi .ts-control > div {
                        background-color: rgb(99, 102, 241);
                        color: white;
                        border: none;
                        border-radius: 0.375rem;
                        padding: 0.25rem 0.5rem;
                        font-size: 0.875rem;
                        margin: 0.25rem;
                    }
                    .ts-wrapper.multi .ts-control > div.active {
                        background-color: rgb(79, 70, 229);
                    }
                    .ts-dropdown {
                        border: 1px solid rgb(203, 213, 225);
                        border-radius: 0.75rem;
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                        background-color: white;
                        margin-top: 0.5rem;
                    }
                    .ts-dropdown .ts-dropdown-content .option {
                        padding: 0.5rem 0.75rem;
                        color: rgb(15, 23, 42);
                    }
                    .ts-dropdown .ts-dropdown-content .option.selected,
                    .ts-dropdown .ts-dropdown-content .option.highlighted {
                        background-color: rgb(99, 102, 241);
                        color: white;
                    }
                    .dark .ts-control {
                        border-color: rgb(71, 85, 105);
                        background-color: rgb(30, 41, 59);
                        color: white;
                    }
                    .dark .ts-control:focus-within {
                        border-color: rgb(99, 102, 241);
                        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
                    }
                    .dark .ts-wrapper.multi .ts-control > div {
                        background-color: rgb(99, 102, 241);
                        color: white;
                    }
                    .dark .ts-dropdown {
                        border-color: rgb(71, 85, 105);
                        background-color: rgb(30, 41, 59);
                    }
                    .dark .ts-dropdown .ts-dropdown-content .option {
                        color: rgb(226, 232, 240);
                    }
                    .dark .ts-dropdown .ts-dropdown-content .option.selected,
                    .dark .ts-dropdown .ts-dropdown-content .option.highlighted {
                        background-color: rgb(99, 102, 241);
                        color: white;
                    }
                </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new TomSelect('#categories', {
                            create: false,
                            placeholder: 'Pilih satu atau lebih kategori...',
                            allowEmptyOption: false,
                            maxItems: null,
                        });
                    });
                </script>
            @endpush

            <!-- Title Field -->
            <!-- Author Name (Admin can set when not linking to participant) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="author_name" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Nama Penulis (Opsional)
                </label>
                <input
                    type="text"
                    id="author_name"
                    name="author_name"
                    value="{{ old('author_name') }}"
                    placeholder="Masukkan nama penulis jika tidak terdaftar sebagai peserta"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900"
                />
                @error('author_name')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="title" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Judul Buku <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Masukkan judul buku"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900 @error('title') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
                />
                @error('title')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subtitle Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="subtitle" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Subjudul (Opsional)
                </label>
                <input
                    type="text"
                    id="subtitle"
                    name="subtitle"
                    value="{{ old('subtitle') }}"
                    placeholder="Masukkan subjudul buku"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900"
                />
            </div>

            <!-- Description Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="desc" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Deskripsi (Opsional)
                </label>
                <textarea
                    id="desc"
                    name="desc"
                    rows="4"
                    placeholder="Masukkan deskripsi buku"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900">{{ old('desc') }}</textarea>
            </div>

            <!-- Language Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="language" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Bahasa (Opsional)
                </label>
                <input
                    type="text"
                    id="language"
                    name="language"
                    value="{{ old('language', 'id') }}"
                    placeholder="Contoh: id, en, jv"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900"
                />
            </div>

            <!-- Color Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="color_hex" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    Warna Hex (Opsional)
                </label>
                <div class="flex gap-3">
                    <input
                        type="color"
                        id="color_hex"
                        name="color_hex"
                        value="{{ old('color_hex', '#000000') }}"
                        class="h-12 w-20 rounded-xl border border-slate-300 dark:border-slate-700"
                    />
                    <input
                        type="text"
                        placeholder="#000000"
                        value="{{ old('color_hex', '#000000') }}"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900"
                        readonly
                    />
                </div>
            </div>

            <!-- Photo Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="photo" class="block text-sm font-medium text-slate-900 dark:text-white mb-4">
                    Foto Buku (Opsional)
                </label>

                <div class="relative">
                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        accept="image/*"
                        class="hidden"
                    />

                    <!-- Upload Area -->
                    <label for="photo" class="block cursor-pointer">
                        <div id="photoDropZone" class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center transition hover:border-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950/20">
                            <div id="photoEmptyState" class="space-y-3">
                                <div class="flex justify-center">
                                    <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">Klik atau drag foto ke sini</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>

                            <div id="photoPreviewState" class="hidden space-y-3">
                                <img id="photoPreview" src="" alt="Preview" class="w-32 h-40 object-cover mx-auto rounded-lg">
                                <p id="photoFileName" class="text-sm font-medium text-slate-900 dark:text-white"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Klik untuk mengganti</p>
                            </div>
                        </div>
                    </label>

                    <!-- Remove Button -->
                    <button
                        id="photoRemoveBtn"
                        type="button"
                        class="hidden absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                @error('photo')
                    <p class="mt-3 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const photoInput = document.getElementById('photo');
                    const photoDropZone = document.getElementById('photoDropZone');
                    const photoEmptyState = document.getElementById('photoEmptyState');
                    const photoPreviewState = document.getElementById('photoPreviewState');
                    const photoPreview = document.getElementById('photoPreview');
                    const photoFileName = document.getElementById('photoFileName');
                    const photoRemoveBtn = document.getElementById('photoRemoveBtn');

                    // Drag and drop
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        photoDropZone.addEventListener(eventName, preventDefaults, false);
                    });

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    ['dragenter', 'dragover'].forEach(eventName => {
                        photoDropZone.addEventListener(eventName, () => {
                            photoDropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/20');
                        });
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        photoDropZone.addEventListener(eventName, () => {
                            photoDropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/20');
                        });
                    });

                    photoDropZone.addEventListener('drop', (e) => {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        photoInput.files = files;
                        updatePhotoPreview();
                    });

                    photoInput.addEventListener('change', updatePhotoPreview);

                    photoRemoveBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        photoInput.value = '';
                        updatePhotoPreview();
                    });

                    function updatePhotoPreview() {
                        if (photoInput.files && photoInput.files[0]) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview.src = e.target.result;
                                photoFileName.textContent = photoInput.files[0].name;
                                photoEmptyState.classList.add('hidden');
                                photoPreviewState.classList.remove('hidden');
                                photoRemoveBtn.classList.remove('hidden');
                            };
                            reader.readAsDataURL(photoInput.files[0]);
                        } else {
                            photoEmptyState.classList.remove('hidden');
                            photoPreviewState.classList.add('hidden');
                            photoRemoveBtn.classList.add('hidden');
                        }
                    }
                });
            </script>

            <!-- PDF File Field -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label for="filename" class="block text-sm font-medium text-slate-900 dark:text-white mb-2">
                    File PDF (Opsional)
                </label>
                <input
                    type="file"
                    id="filename"
                    name="filename"
                    accept=".pdf"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-slate-900 placeholder-slate-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-900 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-white file:font-medium file:cursor-pointer hover:file:bg-indigo-700"
                />
                @error('filename')
                    <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Approval Status -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_approved"
                        value="1"
                        {{ old('is_approved') ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Setujui buku ini sekarang</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan Buku
                </button>
                <a href="{{ route('admin.perpossagar-books.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
