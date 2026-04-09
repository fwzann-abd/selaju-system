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
        <form action="{{ route('admin.classrooms.store') }}" method="POST"
              x-data="(function(){ return {
                  name: @js(old('name', '')),
                  tingkat: @js(old('tingkat', '')),
                  jurusan: @js(old('jurusan', '')),
                  academicYear: @js(old('academic_year')) || `${new Date().getFullYear()}/${new Date().getFullYear() + 1}`,
                  init() {},
                  generateName() {
                      if (this.tingkat && this.jurusan && this.academicYear) {
                          const map = {
                              'X': '10',
                              'XI': '11',
                              'XII': '12'
                          };
                          const tingkatNum = map[this.tingkat] || this.tingkat;
                          const jurusanShort = this.jurusan.substring(0, 1).toUpperCase();
                          this.name =  tingkatNum + ' ' + jurusanShort + ' 1';
                      }
                  }
              } })()"
              class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Nama Kelas <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           placeholder="Contoh: 11 PPL 2"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tingkat" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Tingkatan <span class="text-red-500">*</span></label>
                    <select id="tingkat" name="tingkat" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('tingkat') border-red-500 @enderror"
                            @change="generateName()" required>
                        <option value="">-- Pilih Tingkatan --</option>
                        @foreach($tingkatan as $value => $label)
                            <option value="{{ $value }}" {{ old('tingkat') == $value ? 'selected' : '' }} x-bind:selected="tingkat === '{{ $value }}'">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('tingkat')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jurusan" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Jurusan <span class="text-red-500">*</span></label>
                    <select id="jurusan" name="jurusan" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('jurusan') border-red-500 @enderror"
                            @change="generateName()" required>
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusan as $value => $label)
                            <option value="{{ $value }}" {{ old('jurusan') == $value ? 'selected' : '' }} x-bind:selected="jurusan === '{{ $value }}'">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('jurusan')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Tahun Ajaran <span class="text-red-500">*</span></label>
                    <select id="academic_year" name="academic_year" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('academic_year') border-red-500 @enderror"
                            @change="generateName()" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year }}" {{ old('academic_year') == $year ? 'selected' : '' }} x-bind:selected="academicYear === '{{ $year }}'">
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="teacher_id" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Wali Kelas</label>
                    <select id="teacher_id" name="teacher_id" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('teacher_id') border-red-500 @enderror">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Opsional. Bisa diisi nanti jika diperlukan.</p>
                    @error('teacher_id')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="rombel" class="block text-sm font-semibold text-slate-600 dark:text-slate-200 mb-2">Rombel</label>
                    <input type="text" id="rombel" name="rombel" value="{{ old('rombel') }}" 
                           placeholder="Contoh: 1, 2, 3, A, B, dst"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:border-indigo-400 @error('rombel') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Opsional</p>
                    @error('rombel')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                <a href="{{ route('admin.classrooms.index') }}" 
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    <i class="fa-solid fa-check text-xs"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // Trigger name generation on page load if all fields are filled
            const tingkat = document.querySelector('#tingkat');
            const jurusan = document.querySelector('#jurusan');
            const academicYear = document.querySelector('#academic_year');
            
            if (tingkat.value && jurusan.value && academicYear.value && !document.querySelector('#name').value) {
                // Will be handled by AlpineJS on init
            }
        });
    </script>
    @endpush
</x-app-layout>
