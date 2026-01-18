<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <a href="{{ route('admin.bank-accounts.index') }}" class="hover:text-slate-700 dark:hover:text-slate-300">Akun Bank</a>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Edit Akun Bank</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Edit Akun Bank</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 md:p-8">
            <form action="{{ route('admin.bank-accounts.update', $bankAccount->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-slate-900 dark:text-slate-100">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required
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
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" required
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
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required
                           class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100"
                           placeholder="Contoh: 1234567890">
                    @error('account_number')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-slate-900 dark:text-slate-100">
                        Logo (Maks 100KB)
                    </label>
                    @if($bankAccount->logo)
                        <div class="mt-2 mb-4 flex items-center gap-4">
                            <img src="{{ asset('assets/modules/bank-accounts/logos/' . $bankAccount->logo) }}"
                                 alt="{{ $bankAccount->bank_name }}"
                                 class="h-16 w-16 rounded object-cover">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Logo saat ini</span>
                        </div>
                    @endif
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm file:border-0 file:bg-indigo-50 file:text-indigo-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">PNG, JPG, atau WebP. Maks 100KB. Kosongkan jika tidak ingin mengubah.</p>
                    @error('logo')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div>
                    <label for="is_active" class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ $bankAccount->is_active ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:border-slate-700">
                        <span class="text-sm font-medium text-slate-900 dark:text-slate-100">Aktif</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        <i class="fa-solid fa-check text-xs"></i>
                        Simpan Perubahan
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
