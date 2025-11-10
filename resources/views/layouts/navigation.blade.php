<nav x-data="{ open: false }"
    class="sticky top-0 z-30 border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
    <div class="flex items-center gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <button type="button"
            class="-ml-2 rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 sm:-ml-3 lg:-ml-5"
            @click="$store.layout.toggleMobileSidebar()" aria-label="Toggle sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" />
            </svg>
        </button>

        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="hidden md:flex md:flex-col leading-tight">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Selamat datang kembali,</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <div class="hidden md:block">
                    <label class="relative block">
                        <span class="sr-only">Cari</span>
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
                            </svg>
                        </span>
                        <input type="search" placeholder="Cari di dashboard"
                            class="w-64 rounded-full border border-transparent bg-slate-100 py-2 pl-9 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:bg-slate-800 dark:text-slate-200" />
                    </label>
                </div>

                <button type="button"
                    class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                    @click="$store.layout.toggleTheme()" aria-label="Ganti mode tampilan">
                    <svg x-show="$store.layout.theme === 'light'" x-cloak xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="$store.layout.theme === 'dark'" x-cloak xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9 9 0 1020.354 15.354z" />
                    </svg>
                </button>

                <div class="hidden sm:flex sm:items-center sm:gap-2">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-3 rounded-full border border-transparent bg-slate-100 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/10 text-sm font-semibold uppercase text-indigo-600 dark:text-indigo-300">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                        aria-label="Toggle user menu">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div
            class="space-y-1 border-t border-slate-200 bg-white px-4 py-3 text-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-1">
                <span class="font-medium text-slate-900 dark:text-white">{{ Auth::user()->name }}</span>
                <span class="text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</span>
            </div>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf

                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
