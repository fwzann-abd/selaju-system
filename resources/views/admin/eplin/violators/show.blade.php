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

        <!-- Student Info Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $student->name }}</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-medium">NIS:</span> {{ $student->student_number }}
                    </p>
                </div>
                <div class="flex flex-col gap-2 sm:text-right">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Total Pelanggaran</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $violations->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Violations Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/50">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">TANGGAL</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">JENIS PELANGGARAN</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">CATATAN</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">STATUS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-900 dark:text-slate-100">DICATAT OLEH</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-900 dark:text-slate-100">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($violations as $violation)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                    {{ $violation->violation_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $violation->violationType?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    <span class="line-clamp-1">{{ $violation->description ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'recorded' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300',
                                            'under_review' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                            'verified' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                                            'dismissed' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-300',
                                        ];
                                        $statusLabels = [
                                            'recorded' => 'Tercatat',
                                            'under_review' => 'Review',
                                            'verified' => 'Terverifikasi',
                                            'dismissed' => 'Ditutup',
                                        ];
                                    @endphp
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$violation->status] ?? 'bg-gray-100' }}">
                                        {{ $statusLabels[$violation->status] ?? $violation->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $violation->recordedByOfficer?->student?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                x-data="{
                                                    id: '{{ $violation->id }}',
                                                    data: {
                                                        date: '{{ $violation->violation_date->format('d M Y') }}',
                                                        type: '{{ $violation->violationType?->name ?? '-' }}',
                                                        description: '{{ $violation->description ?? '-' }}',
                                                        evidence: '{{ $violation->evidence ?? '-' }}',
                                                        status: '{{ $violation->status }}',
                                                        officer: '{{ $violation->recordedByOfficer?->student?->name ?? '-' }}'
                                                    }
                                                }"
                                                @click="$dispatch('open-view-modal', data)"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Lihat
                                        </button>
                                        <a href="{{ route('admin.eplin.violators.edit', $violation->id) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Edit
                                        </a>
                                        <button type="button"
                                                onclick="deleteViolation('{{ $violation->id }}', '{{ $violation->violationType?->name ?? 'Pelanggaran' }}')"
                                                class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fa-solid fa-inbox text-3xl text-slate-300 dark:text-slate-600"></i>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Tidak ada data pelanggaran.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $violations->links() }}
    </div>

    <script>
        function deleteViolation(violationId, violationType) {
            Swal.fire({
                title: 'Hapus Pelanggaran',
                text: `Pilih metode penghapusan untuk "${violationType}":`,
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

                    confirmBtn.onclick = () => submitViolationDelete(violationId, 'soft');
                    permanentBtn.onclick = () => submitViolationDelete(violationId, 'permanent');
                }
            });
        }

        function submitViolationDelete(violationId, type) {
            const url = type === 'permanent'
                ? `/admin/eplin/violators/${violationId}/force`
                : `/admin/eplin/violators/${violationId}`;

            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                text: type === 'permanent'
                    ? 'Data akan dihapus secara permanen dan tidak dapat dipulihkan. Lanjutkan?'
                    : 'Data akan disimpan dalam backup. Lanjutkan?',
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
