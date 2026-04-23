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

    @include('admin.classrooms._form', [
        'action' => route('admin.classrooms.update', $classroom),
        'method' => 'PUT',
        'buttonLabel' => 'Simpan Perubahan',
        'classroom' => $classroom,
        'tingkatan' => $tingkatan,
        'jurusan' => $jurusan,
        'academicYears' => $academicYears,
        'teachers' => $teachers,
    ])
</x-app-layout>
