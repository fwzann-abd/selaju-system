@props(['disabled' => false])

<div x-data="passwordToggle()" class="relative">
    <input {{ $disabled ? 'disabled' : '' }}
        x-ref="passwordInput"
        type="password"
        {!! $attributes->merge(['class' => 'block w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-12 text-gray-900 shadow-sm placeholder-gray-400 transition-all duration-200 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-300']) !!}>
    <button type="button"
        x-on:click="toggle()"
        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition-colors hover:text-gray-600 focus:outline-none dark:hover:text-gray-300"
        tabindex="-1">
        {{-- Eye open (password hidden) --}}
        <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        {{-- Eye closed (password visible) --}}
        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
        </svg>
    </button>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('passwordToggle', () => ({
        show: false,
        toggle() {
            this.show = !this.show;
            this.$refs.passwordInput.type = this.show ? 'text' : 'password';
        }
    }));
});
</script>
@endpush
@endonce
