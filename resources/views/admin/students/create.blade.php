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

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="school_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Sekolah</label>
                    <select id="school_id" name="school_id" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->id }}" @if(old('school_id') == $sch->id) selected @endif>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama</label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nama Siswa">
                    @error('nama')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nipd" class="text-sm font-semibold text-slate-600 dark:text-slate-200">NIPD</label>
                    <input id="nipd" name="nipd" type="text" value="{{ old('nipd') }}"
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nomor Induk Peserta Didik (opsional)">
                    @error('nipd')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nisn" class="text-sm font-semibold text-slate-600 dark:text-slate-200">NISN</label>
                    <input id="nisn" name="nisn" type="text" value="{{ old('nisn') }}" required
                        class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nomor Induk Siswa Nasional">
                    @error('nisn')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jk" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Jenis Kelamin</label>
                    <select id="jk" name="jk" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" @if(old('jk') == 'L') selected @endif>Laki-laki</option>
                        <option value="P" @if(old('jk') == 'P') selected @endif>Perempuan</option>
                    </select>
                    @error('jk')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.students.index') }}" class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
                <button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700">Simpan Siswa</button>
            </div>
        </form>
    </div>
</x-app-layout>
