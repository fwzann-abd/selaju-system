<div class="space-y-6">
    @if ($errors->any())
        <div class="rounded-xl border border-rose-300/40 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-200">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="{{ $action }}" method="POST" class="space-y-6">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="name">Nama Mata Pelajaran <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $subject?->name) }}" required
                           placeholder="Contoh: Matematika"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="code">Kode</label>
                    <input id="code" name="code" type="text" value="{{ old('code', $subject?->code) }}"
                           placeholder="Contoh: MTK"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200 @error('code') border-red-500 @enderror">
                    @error('code')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="type">Tipe</label>
                    <select id="type" name="type"
                            class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200 @error('type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $subject?->type) == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.subjects.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    {{ $buttonLabel }}
                </button>
            </div>
        </form>
    </div>
</div>
