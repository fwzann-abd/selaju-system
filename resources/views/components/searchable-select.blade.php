@props([
    'name',
    'id' => null,
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'selected' => null,
    'placeholder' => 'Pilih...',
    'searchPlaceholder' => 'Cari...',
    'required' => false,
    'emptyLabel' => null,
])

@php
    $componentId = $id ?? $name;
    $selectedValue = old($name, $selected);
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selected: '{{ $selectedValue }}',
        selectedLabel: '',
        options: @js($options->map(fn($o) => ['value' => data_get($o, $valueField), 'label' => data_get($o, $labelField)])->values()),
        get filtered() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.label.toLowerCase().includes(q));
        },
        init() {
            const found = this.options.find(o => String(o.value) === String(this.selected));
            this.selectedLabel = found ? found.label : '';
        },
        pick(opt) {
            this.selected = opt.value;
            this.selectedLabel = opt.label;
            this.search = '';
            this.open = false;
        },
        clear() {
            this.selected = '';
            this.selectedLabel = '';
            this.search = '';
        }
    }"
    @click.away="open = false; search = ''"
    class="relative"
>
    <input type="hidden" name="{{ $name }}" :value="selected">

    {{-- Trigger button --}}
    <button
        type="button"
        @click="open = !open; $nextTick(() => { if(open) $refs.searchInput.focus() })"
        id="{{ $componentId }}"
        class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-transparent px-3 py-3 text-left text-sm text-slate-700 focus:border-indigo-500 focus:outline-none dark:border-slate-700 dark:text-slate-200"
    >
        <span x-text="selectedLabel || '{{ $placeholder }}'" :class="!selectedLabel && 'text-slate-400 dark:text-slate-500'"></span>
        <span class="flex items-center gap-1">
            <button type="button" x-show="selected" @click.stop="clear()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-0.5">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="open && 'rotate-180'"></i>
        </span>
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute z-50 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800"
    >
        {{-- Search input --}}
        <div class="border-b border-slate-200 p-2 dark:border-slate-700">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                <input
                    type="text"
                    x-ref="searchInput"
                    x-model="search"
                    placeholder="{{ $searchPlaceholder }}"
                    class="w-full rounded-lg border-0 bg-slate-50 py-2 pl-8 pr-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 dark:bg-slate-900 dark:text-white dark:placeholder-slate-500 dark:focus:bg-slate-800"
                    @keydown.escape="open = false; search = ''"
                >
            </div>
        </div>

        {{-- Options list --}}
        <ul class="max-h-48 overflow-y-auto p-1">
            @if($emptyLabel)
                <li>
                    <button
                        type="button"
                        @click="pick({ value: '', label: '' })"
                        class="flex w-full items-center rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700"
                    >
                        {{ $emptyLabel }}
                    </button>
                </li>
            @endif
            <template x-for="opt in filtered" :key="opt.value">
                <li>
                    <button
                        type="button"
                        @click="pick(opt)"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition"
                        :class="String(selected) === String(opt.value)
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300'
                            : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'"
                    >
                        <span x-text="opt.label"></span>
                        <i x-show="String(selected) === String(opt.value)" class="fa-solid fa-check text-xs text-indigo-600 dark:text-indigo-400"></i>
                    </button>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3 py-4 text-center text-sm text-slate-400 dark:text-slate-500">
                Tidak ditemukan
            </li>
        </ul>
    </div>
</div>
