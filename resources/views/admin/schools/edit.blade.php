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
        <form action="{{ route('admin.schools.update', $school) }}" method="POST"
              x-data="schoolForm({ initialName: @js(old('name', $school->name)), initialSlug: @js(old('slug', $school->slug)) })"
              class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="name" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Nama Sekolah</label>
                    <input id="name" name="name" type="text" x-model="name" @input="handleNameInput"
                           required
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           placeholder="SMKN 1 Selaju">
                    @error('name')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="slug" class="text-sm font-semibold text-slate-600 dark:text-slate-200">Slug</label>
                    <input id="slug" name="slug" type="text" x-model="slug" @input="handleSlugInput" @focus="slugTouched = true"
                           required
                           class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                           placeholder="smkn-1-selaju">
                    <p class="mt-1 text-xs text-slate-400">Sesuaikan slug bila diperlukan.</p>
                    @error('slug')
                        <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-between border-t border-slate-100 pt-4 dark:border-slate-800">
                <button type="button"
                        class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-500/40 dark:text-red-300 dark:hover:bg-red-500/10"
                        onclick="if(confirm('Yakin ingin menghapus sekolah ini?')) document.getElementById('delete-school-{{ $school->id }}').submit()">
                    Hapus
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('admin.schools.index') }}"
                       class="rounded-2xl border border-slate-200 px-5 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                        Kembali
                    </a>
                    <button type="submit"
                            class="rounded-2xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <form id="delete-school-{{ $school->id }}" action="{{ route('admin.schools.destroy', $school) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-app-layout>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('schoolForm', ({ initialName = '', initialSlug = '' } = {}) => ({
                    name: initialName,
                    slug: initialSlug,
                    slugTouched: initialSlug.length > 0,
                    init() {
                        if (!this.slug) {
                            this.slug = this.slugify(this.name)
                        }
                    },
                    slugify(value) {
                        return (value ?? '')
                            .toString()
                            .toLowerCase()
                            .trim()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '')
                    },
                    handleNameInput() {
                        if (!this.slugTouched || !this.slug) {
                            this.slug = this.slugify(this.name)
                        }
                    },
                    handleSlugInput() {
                        this.slug = this.slugify(this.slug)
                        this.slugTouched = true
                    }
                }))
            })
        </script>
    @endpush
@endonce
