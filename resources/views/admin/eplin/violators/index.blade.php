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

    <!-- SweetAlert2 Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="{{ route('admin.eplin.violators.index') }}" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau NIS siswa"
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    @if($search)
                        <a href="{{ route('admin.eplin.violators.index') }}" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">NAMA SISWA</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">JUMLAH PELANGGARAN</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">PELANGGARAN TERAKHIR</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-900 dark:text-slate-100">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($violators as $violator)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $violator->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ $violator->student_number }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">
                                        {{ $violator->violations_count }} Pelanggaran
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    @if($violator->violations->first())
                                        {{ $violator->violations->first()->violation_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.eplin.violators.show', $violator->id) }}"
                                           class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Lihat Detail
                                        </a>
                                        <button type="button"
                                                onclick="deleteViolator('{{ $violator->id }}', '{{ $violator->name }}')"
                                                class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-solid fa-inbox text-3xl text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data pelanggar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $violators->links() }}
    </div>

    <script>
        function deleteViolator(violatorId, violatorName) {
            Swal.fire({
                title: 'Hapus Pelanggar',
                text: `Pilih metode penghapusan untuk ${violatorName}:`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus (Backup)',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                didOpen: (modal) => {
                    const confirmBtn = modal.querySelector('.swal2-confirm');
                    const cancelBtn = modal.querySelector('.swal2-cancel');

                    // Tambah tombol Hapus Permanen
                    const permanentBtn = document.createElement('button');
                    permanentBtn.className = 'swal2-confirm swal2-styled mx-2 bg-red-600';
                    permanentBtn.textContent = 'Hapus Permanen';
                    permanentBtn.style.display = 'none';
                    modal.querySelector('.swal2-actions').insertBefore(permanentBtn, confirmBtn.nextSibling);

                    confirmBtn.onclick = () => submitDelete(violatorId, 'soft');
                    permanentBtn.onclick = () => submitDelete(violatorId, 'permanent');
                }
            });
        }

        function submitDelete(violatorId, type) {
            const url = type === 'permanent'
                ? `/admin/eplin/violator-students/${violatorId}/force`
                : `/admin/eplin/violator-students/${violatorId}`;

            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                text: type === 'permanent'
                    ? 'Data pelanggar dan semua pelanggaran akan dihapus secara permanen dan tidak dapat dipulihkan. Lanjutkan?'
                    : 'Data pelanggar dan semua pelanggaran akan disimpan dalam backup. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
