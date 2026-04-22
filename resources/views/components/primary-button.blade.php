<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-3 bg-[#136dec] border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide shadow-lg shadow-blue-500/25 hover:bg-[#1a7fff] hover:shadow-blue-500/35 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 active:bg-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 cursor-pointer']) }}>
    {{ $slot }}
</button>
