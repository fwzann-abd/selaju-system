<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.bank-accounts.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Akun Bank</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Tambah Akun Bank</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Tambah Akun Bank</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:p-8">
            <form action="{{ route('admin.bank-accounts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-slate-900 dark:text-slate-100">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" required
                           class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                           placeholder="Contoh: Bank Mandiri">
                    @error('bank_name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-slate-900 dark:text-slate-100">
                        Nama Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}" required
                           class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                           placeholder="Contoh: Yayasan Selaju">
                    @error('account_name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-slate-900 dark:text-slate-100">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number') }}" required
                           class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                           placeholder="Contoh: 1234567890">
                    @error('account_number')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-slate-900 dark:text-slate-100 mb-2">
                        Logo Bank <span class="text-slate-500 font-normal">(Maks 100KB)</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="sr-only" onchange="handleLogoChange(event)">
                        <label for="logo" class="flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-8 text-center cursor-pointer transition hover:border-indigo-400 hover:bg-indigo-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-indigo-400 dark:hover:bg-slate-700">
                            <div class="rounded-lg bg-indigo-100 p-3 dark:bg-indigo-900">
                                <i class="fa-solid fa-image text-xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">Pilih logo atau tarik ke sini</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PNG, JPG, atau WebP</p>
                            </div>
                        </label>
                        <div id="logo-preview" class="hidden mt-4 flex items-center gap-4 p-4 rounded-lg bg-slate-100 dark:bg-slate-800">
                            <img id="logo-preview-img" src="" alt="Preview" class="h-12 w-12 rounded object-cover">
                            <div class="flex-1">
                                <p id="logo-preview-name" class="text-sm font-medium text-slate-900 dark:text-white"></p>
                                <p id="logo-preview-size" class="text-xs text-slate-500 dark:text-slate-400"></p>
                            </div>
                            <button type="button" onclick="clearLogoPreview()" class="text-slate-500 hover:text-red-600 dark:hover:text-red-400">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @error('logo')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400"><i class="fa-solid fa-circle-exclamation text-xs mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                <script>
                    function handleLogoChange(event) {
                        const file = event.target.files[0];
                        if (!file) return;

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('logo-preview-img').src = e.target.result;
                            document.getElementById('logo-preview-name').textContent = file.name;
                            document.getElementById('logo-preview-size').textContent = (file.size / 1024).toFixed(2) + ' KB';
                            document.getElementById('logo-preview').classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }

                    function clearLogoPreview() {
                        document.getElementById('logo').value = '';
                        document.getElementById('logo-preview').classList.add('hidden');
                    }
                </script>

                <!-- Is Active -->
                <div>
                    <label for="is_active" class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:border-slate-700">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-100">Aktif</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        <i class="fa-solid fa-check text-xs"></i>
                        Simpan
                    </button>
                    <a href="{{ route('admin.bank-accounts.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                        <i class="fa-solid fa-times text-xs"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
