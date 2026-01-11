<x-app-layout>
    <div class="space-y-4 sm:space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Detail Transfer Manual</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">ID: {{ $manualTransfer->id }}</p>
            </div>
            <a href="{{ route('admin.manual-transfers.index') }}" class="rounded-lg bg-slate-600 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                ← Kembali
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <!-- Informasi Donasi -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Informasi Donasi</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nama Donatur</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                            {{ $manualTransfer->donation->donor_name ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nominal Donasi</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($manualTransfer->donation->amount, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Pesan</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ $manualTransfer->donation->message ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Informasi Rekening -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Informasi Rekening Transfer</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nama Bank</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                            {{ $manualTransfer->bank_name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Nomor Rekening</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                            {{ $manualTransfer->account_number }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Atas Nama</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ $manualTransfer->account_holder_name }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Detail Transfer -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Detail Transfer</h2>
                <dl class="mt-4 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Jumlah Transfer</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($manualTransfer->transfer_amount, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Transfer</dt>
                        <dd class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                            {{ $manualTransfer->transfer_date->format('d F Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Status Verifikasi</dt>
                        <dd class="mt-1">
                            @if($manualTransfer->verification_status === 'verified')
                                <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    Terverifikasi
                                </span>
                            @elseif($manualTransfer->verification_status === 'rejected')
                                <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Menunggu Verifikasi
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Bukti Transfer -->
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Bukti Transfer</h2>
                @if($manualTransfer->transfer_proof)
                    <div class="mt-4">
                        <img src="{{ Storage::url($manualTransfer->transfer_proof) }}" alt="Bukti Transfer" class="rounded-lg max-h-96 w-full object-cover">
                        <a href="{{ Storage::url($manualTransfer->transfer_proof) }}" target="_blank" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                            Lihat Gambar Penuh
                        </a>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Belum ada bukti transfer</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
