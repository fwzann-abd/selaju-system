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

    <div class="max-w-2xl">
        <form action="{{ route('admin.eplin.officers.update', $officer) }}" method="POST" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @csrf
            @method('PUT')

            <div>
                <label for="student_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Siswa <span class="text-red-500">*</span></label>
                <select id="student_id" name="student_id" required
                        class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="">— Pilih Siswa —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id') === $student->id || $officer->student_id === $student->id)>
                            {{ $student->nama }} ({{ $student->nisn }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Jabatan <span class="text-red-500">*</span></label>
                <select id="role" name="role" required
                        class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                    <option value="petugas" @selected(old('role') === 'petugas' || $officer->role === 'petugas')>Petugas</option>
                </select>
                @error('role')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') ?? $officer->start_date }}" required
                       class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                @error('start_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Selesai (Opsional)</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') ?? $officer->end_date }}"
                       class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                @error('end_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                <div class="mt-2 flex items-center gap-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $officer->is_active))
                               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700 dark:text-slate-300">Aktif</span>
                    </label>
                </div>
                @error('is_active')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-save text-xs"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.eplin.officers.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-6 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
