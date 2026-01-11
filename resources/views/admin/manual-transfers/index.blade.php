<x-app-layout>
    <div class="space-y-4 sm:space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Transfer Manual</h1>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Kelola daftar transfer manual dari donatur</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nama Pendonasi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nama Bank</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nomor Rekening</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Jumlah Transfer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Tanggal Transfer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($transfers as $transfer)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4 text-sm text-slate-900 dark:text-slate-100">
                                    {{ $transfer->donation->donor_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $transfer->bank_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $transfer->account_number }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                    Rp {{ number_format($transfer->transfer_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $transfer->transfer_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($transfer->verification_status === 'verified')
                                        <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            Terverifikasi
                                        </span>
                                    @elseif($transfer->verification_status === 'rejected')
                                        <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.manual-transfers.show', $transfer->id) }}" class="font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada data transfer manual
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($transfers->hasPages())
            <div class="flex justify-center">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
