@props(['message' => 'Belum ada data.', 'icon' => 'fa-solid fa-inbox'])

<div class="rounded-xl p-12 text-center">
    <i class="{{ $icon }} text-4xl text-slate-300 dark:text-slate-600 mb-4 inline-block"></i>
    <p class="text-slate-500 dark:text-slate-400">{{ $message }}</p>
</div>
