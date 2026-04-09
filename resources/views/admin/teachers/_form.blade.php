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
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="school_id">Sekolah</label>
                    <select id="school_id" name="school_id" required
                            class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                        <option value="">Pilih sekolah</option>
                        @foreach($schools as $schoolOption)
                            <option value="{{ $schoolOption->id }}"
                                @selected(old('school_id', $teacher?->school_id) == $schoolOption->id)>
                                {{ $schoolOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="name">Nama Guru</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $teacher?->name) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="nip">NIP</label>
                    <input id="nip" name="nip" type="text" value="{{ old('nip', $teacher?->nip) }}"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username', $teacher?->account->username ?? '') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $teacher?->account->email ?? '') }}" required
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="password">Password</label>
                    <input id="password" name="password" type="password" @if($method !== 'PUT') required @endif
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                    @if($method === 'PUT')
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Biarkan kosong jika tidak ingin mengubah password.</p>
                    @endif
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600 dark:text-slate-200" for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="w-full rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.teachers.index') }}"
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
