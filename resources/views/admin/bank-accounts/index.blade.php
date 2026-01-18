<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                <span>Admin</span>
                <span class="mx-2">/</span>
                <span class="text-slate-900 dark:text-white">Akun Bank</span>
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Akun Bank</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-300/40 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <form action="{{ route('admin.bank-accounts.index') }}" method="GET" class="w-full md:max-w-sm">
                <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm focus-within:border-indigo-500 dark:border-slate-700 dark:bg-slate-900">
                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama bank..."
                           class="w-full border-none bg-transparent text-sm text-slate-700 placeholder-slate-400 focus:ring-0 dark:text-slate-200"
                           autocomplete="off">
                    @if(request('q'))
                        <a href="{{ route('admin.bank-accounts.index') }}" class="text-xs text-indigo-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.bank-accounts.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Akun Bank
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Logo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Bank</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nama Rekening</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Nomor Rekening</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($bankAccounts as $account)
                            <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="px-6 py-4">
                                    @if($account->logo)
                                        <img src="{{ asset('assets/modules/bank-accounts/logos/' . $account->logo) }}"
                                             alt="{{ $account->bank_name }}"
                                             class="h-10 w-10 rounded object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded bg-slate-200 dark:bg-slate-700"></div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                    {{ $account->bank_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $account->account_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 font-mono">
                                    {{ $account->account_number }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($account->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                            <span class="h-2 w-2 rounded-full bg-slate-600"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm space-x-3">
                                    <a href="{{ route('admin.bank-accounts.edit', $account->id) }}"
                                       class="font-medium text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.bank-accounts.destroy', $account->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus akun bank ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 transition hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Belum ada akun bank
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($bankAccounts->hasPages())
            <div class="flex justify-center">
                {{ $bankAccounts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
