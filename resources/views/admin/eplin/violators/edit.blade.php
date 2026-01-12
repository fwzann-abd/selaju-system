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

    <div class="mx-auto max-w-2xl space-y-6">
        <form action="{{ route('admin.eplin.violators.update', $violation->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Student Info Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $violation->student->name }}</h3>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    <span class="font-medium">NIS:</span> {{ $violation->student->student_number }}
                </p>
            </div>

            <!-- Form Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <!-- Violation Type -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Jenis Pelanggaran
                    </label>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                        {{ $violation->violationType?->name ?? '-' }}
                    </div>
                </div>

                <!-- Violation Date -->
                <div class="mb-6">
                    <label for="violation_date" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Tanggal Pelanggaran
                    </label>
                    <input type="date" id="violation_date" name="violation_date" value="{{ $violation->violation_date->format('Y-m-d') }}" required
                           class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                    @error('violation_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Catatan
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ $violation->description }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Evidence -->
                <div class="mb-6">
                    <label for="evidence" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Bukti/Keterangan
                    </label>
                    <textarea id="evidence" name="evidence" rows="4"
                              class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">{{ $violation->evidence }}</textarea>
                    @error('evidence')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Status
                    </label>
                    <select id="status" name="status" required
                            class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                        <option value="recorded" {{ $violation->status === 'recorded' ? 'selected' : '' }}>Tercatat</option>
                        <option value="under_review" {{ $violation->status === 'under_review' ? 'selected' : '' }}>Review</option>
                        <option value="verified" {{ $violation->status === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="dismissed" {{ $violation->status === 'dismissed' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recorded By Officer -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Dicatat Oleh
                    </label>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">
                        {{ $violation->recordedByOfficer?->student?->name ?? '-' }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                        <i class="fa-solid fa-check text-xs"></i>
                        Simpan
                    </button>
                    <a href="{{ route('admin.eplin.violators.show', $violation->student_id) }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
