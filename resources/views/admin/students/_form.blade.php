{{-- Shared form partial for student create/edit --}}
{{-- @props: $action, $method, $buttonLabel, $student (nullable), $schools --}}

@php
    $isEdit = $student !== null;
@endphp

<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <form action="{{ $action }}" method="POST" class="space-y-6">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="school_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Sekolah</label>
                <select id="school_id" name="school_id" required
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}" @if(old('school_id', $student?->school_id) == $sch->id) selected @endif>{{ $sch->name }}</option>
                    @endforeach
                </select>
                @error('school_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name', $student?->name) }}" required
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nama Siswa">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="student_number" class="text-sm font-semibold text-slate-600 dark:text-slate-200">NIPD</label>
                <input id="student_number" name="student_number" type="text" value="{{ old('student_number', $student?->student_number) }}"
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nomor Induk Peserta Didik (opsional)">
                @error('student_number')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="national_id" class="text-sm font-semibold text-slate-600 dark:text-slate-200">NISN</label>
                <input id="national_id" name="national_id" type="text" value="{{ old('national_id', $student?->national_id) }}" required
                    class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white" placeholder="Nomor Induk Siswa Nasional">
                @error('national_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gender" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Jenis Kelamin</label>
                <select id="gender" name="gender" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" @if(old('gender', $student?->gender) == 'L') selected @endif>Laki-laki</option>
                    <option value="P" @if(old('gender', $student?->gender) == 'P') selected @endif>Perempuan</option>
                </select>
                @error('gender')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.students.index') }}" class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Batal</a>
            <button type="submit" class="rounded-2xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700">{{ $buttonLabel }}</button>
        </div>
    </form>
</div>
