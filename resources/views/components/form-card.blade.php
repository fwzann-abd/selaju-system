@props(['action' => '', 'method' => 'POST', 'buttonLabel' => 'Simpan', 'cancelRoute' => null])

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
            @if(strtoupper($method) === 'PUT' || strtoupper($method) === 'PATCH')
                @method($method)
            @endif

            {{ $slot }}

            <div class="flex flex-wrap items-center justify-end gap-3 pt-4">
                @if($cancelRoute)
                    <a href="{{ $cancelRoute }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-800">
                        Batal
                    </a>
                @endif
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                    {{ $buttonLabel }}
                </button>
            </div>
        </form>
    </div>
</div>
