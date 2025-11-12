<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <nav class="text-sm text-slate-500 dark:text-slate-400">
                @php
                    $breadcrumb = $breadcrumb ?? [ ['label' => 'Articles', 'url' => null] ];
                @endphp
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
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $pageTitle ?? 'Articles' }}</h2>
        </div>
    </x-slot>

    @php
        // fallback sample collection
        $articles = $articles ?? collect([ (object)['id' => 1, 'title' => 'Example Article', 'category' => (object)['name' => 'Example Category'], 'published' => true, 'created_at' => now()] ]);
    @endphp

    <div class="space-y-6">
        <!-- Actions Bar -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm text-slate-600 dark:text-slate-300">
                    Total {{ method_exists($articles, 'total') ? $articles->total() : (is_countable($articles) ? count($articles) : 0) }} articles
                </p>
            </div>
            <a href="{{ route('admin.articles.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                <i class="fa-solid fa-plus text-xs"></i>
                New Article
            </a>
        </div>

        <!-- Articles Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Title</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Published</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Created</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                        @foreach($articles as $article)
                        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $article->title }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $article->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ $article->published ? 'Yes' : 'No' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ isset($article->created_at) ? \Illuminate\Support\Carbon::parse($article->created_at)->format('d M Y') : '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.articles.edit', $article->id) }}"
                                       class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                                        <i class="fa-solid fa-pencil text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-red-500 transition hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10 dark:hover:text-red-300">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(method_exists($articles, 'hasPages') && $articles->hasPages())
        <div class="flex justify-center">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
