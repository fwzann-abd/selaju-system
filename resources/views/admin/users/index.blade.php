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
        <!-- Actions Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Total {{ $users->total() }} pengguna terdaftar
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus text-xs"></i>
                Tambah Pengguna
            </a>
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Pengguna
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                User Group
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Dibuat
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @foreach($users as $user)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/10 text-sm font-semibold uppercase text-indigo-600 dark:text-indigo-300">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-500/10 dark:text-blue-200">
                                    {{ $user->userGroup?->name ?? 'Belum di-set' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                        <i class="fa-solid fa-pencil text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="flex justify-center">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
