<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @php $breadcrumb = $breadcrumb ?? [ ['label' => 'Articles', 'url' => route('admin.articles.index')], ['label' => 'Details', 'url' => null] ]; @endphp
                @foreach($breadcrumb as $key => $item)
                    @if($loop->last)
                        <span class="text-slate-900 dark:text-white">{{ $item['label'] }}</span>
                    @else
                        @if(!empty($item['url']))
                            <a href="{{ $item['url'] }}" class="hover:text-slate-700 dark:hover:text-slate-300">{{ $item['label'] }}</a>
                        @else
                            <span>{{ $item['label'] }}</span>
                        @endif
                        <span class="mx-2">/</span>
                    @endif
                @endforeach
            </nav>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">Article Details</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-lg font-semibold">Article: {{ $article->title ?? ($id ?? '—') }}</h3>
            <p class="text-sm text-slate-600 mt-2">Details placeholder. Implement actual fields later.</p>
        </div>
    </div>
</x-app-layout>
