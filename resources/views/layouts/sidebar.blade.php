@php
    $brandName = config('app.name', 'Selaju Admin');
    $brandInitials = (string) \Illuminate\Support\Str::of($brandName)
        ->replaceMatches('/[^A-Za-z0-9]/', '')
        ->substr(0, 2)
        ->upper();
    if ($brandInitials === '') {
        $brandInitials = 'SA';
    }

    // Hardcoded menu structure
    $menus = [
        [
            'icon' => 'home',
            'label' => 'Dashboard',
            'href' => route('dashboard'),
            'active' => ['dashboard'],
        ],
        [
            'icon' => 'users',
            'label' => 'Pengguna',
            'children' => [
                [
                    'label' => 'Daftar Pengguna',
                    'href' => route('admin.participants.index'),
                    'active' => ['admin.participants.index', 'admin.participants.show'],
                ],
            ],
        ],
        [
            'icon' => 'store',
            'label' => 'Sejajan',
            'children' => [
                [
                    'label' => 'Daftar Toko',
                    'href' => route('admin.sejajan.index'),
                    'active' => ['admin.sejajan.index', 'admin.sejajan.show'],
                ],
            ],
        ],
        [
            'icon' => 'book',
            'label' => 'Perpossagar',
            'children' => [
                [
                    'label' => 'Kategori Buku',
                    'href' => route('admin.perpossagar-categories.index'),
                    'active' => ['admin.perpossagar-categories.*'],
                ],
                [
                    'label' => 'Bahasa Buku',
                    'href' => route('admin.perpossagar-book-langs.index'),
                    'active' => ['admin.perpossagar-book-langs.*'],
                ],
                [
                    'label' => 'Daftar Buku',
                    'href' => route('admin.perpossagar-books.index'),
                    'active' => [
                        'admin.perpossagar-books.index',
                        'admin.perpossagar-books.create',
                        'admin.perpossagar-books.edit',
                        'admin.perpossagar-books.show',
                    ],
                ],
                [
                    'label' => 'Setting Hero',
                    'href' => route('admin.perpossagar-books.hero'),
                    'active' => ['admin.perpossagar-books.hero', 'admin.perpossagar-books.hero.update'],
                ],
            ],
        ],
        [
            'icon' => 'gift',
            'label' => 'Selaju',
            'children' => [
                [
                    'label' => 'Transfer Manual',
                    'href' => route('admin.manual-transfers.index'),
                    'active' => ['admin.manual-transfers.index', 'admin.manual-transfers.show'],
                ],
                [
                    'label' => 'Akun Bank',
                    'href' => route('admin.bank-accounts.index'),
                    'active' => ['admin.bank-accounts.*'],
                ],
            ],
        ],
        [
            'icon' => 'users',
            'label' => 'Webex',
            'children' => [
                [
                    'label' => 'Ekskul',
                    'href' => route('admin.webex.ekskul.index'),
                    'active' => ['admin.webex.ekskul.*'],
                ],
                [
                    'label' => 'Peserta',
                    'href' => '#',
                    'active' => ['admin.webex.peserta.*'],
                ],
                [
                    'label' => 'Laporan',
                    'href' => '#',
                    'active' => ['admin.webex.laporan.*'],
                ],
                [
                    'label' => 'Presensi',/*  */
                    'href' => '#',
                    'active' => ['admin.webex.presensi.*'],
                ],
            ],
        ],
        [
            'icon' => 'shield-check',
            'label' => 'Eplin',
            'children' => [
                [
                    'label' => 'Petugas',
                    'href' => route('admin.eplin.officers.index'),
                    'active' => ['admin.eplin.officers.*'],
                ],
                [
                    'label' => 'Pelanggar',
                    'href' => route('admin.eplin.violators.index'),
                    'active' => ['admin.eplin.violators.*'],
                ],
            ],
        ],
        [
            'icon' => 'newspaper',
            'label' => 'Artikel',
            'children' => [
                [
                    'label' => 'Kategori',
                    'href' => route('admin.article-categories.index'),
                    'active' => ['admin.article-categories.*'],
                ],
                [
                    'label' => 'Daftar Artikel',
                    'href' => route('admin.articles.index'),
                    'active' => ['admin.articles.*'],
                ],
            ],
        ],
        [
            'icon' => 'building',
            'label' => 'Sekolah',
            'children' => [
                [
                    'label' => 'Daftar Sekolah',
                    'href' => route('admin.schools.index'),
                    'active' => ['admin.schools.*'],
                ],
                [
                    'label' => 'Daftar Guru',
                    'href' => route('admin.teachers.index'),
                    'active' => ['admin.teachers.*'],
                ],
                [
                    'label' => 'Daftar Kelas',
                    'href' => route('admin.classrooms.index'),
                    'active' => ['admin.classrooms.*'],
                ],
                [
                    'label' => 'Data Siswa',
                    'href' => route('admin.students.index'),
                    'active' => ['admin.students.*'],
                ],
            ],
        ],
        [
            'icon' => 'book-open',
            'label' => 'LMS Melesat 🎓',
            'children' => [
                [
                    'label' => 'Jadwal KBM',
                    'href' => '/admin/lms/schedules',
                    'active' => ['admin.lms.schedules.*'],
                ],
                [
                    'label' => 'Daftar Kelas',
                    'href' => '/admin/lms/classrooms',
                    'active' => ['admin.lms.classrooms.*'],
                ],
                [
                    'label' => 'Data Guru',
                    'href' => '/admin/lms/teachers',
                    'active' => ['admin.lms.teachers.*'],
                ],
                [
                    'label' => 'Data Siswa',
                    'href' => '/admin/lms/students',
                    'active' => ['admin.lms.students.*'],
                ],
            ],
        ],
        [
            'icon' => 'cog',
            'label' => 'Pengaturan',
            'children' => [
                [
                    'label' => 'Profil',
                    'href' => route('profile.edit'),
                    'active' => ['profile.*'],
                ],
                [
                    'label' => 'Generasi',
                    'href' => route('admin.generations.index'),
                    'active' => ['admin.generations.*'],
                ],
            ],
        ],

    ];

    $navBaseClasses = 'group flex w-full items-center rounded-xl py-3 text-sm font-medium transition-colors cursor-pointer';
    $navExpandedSpacing = 'px-4 gap-3';
    $navCollapsedSpacing = 'px-3 justify-center';
    $navActiveClasses = 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200';
    $navInactiveClasses = 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800';
    $childNavBaseClasses = 'flex items-center gap-3 rounded-xl px-4 py-2 text-sm transition-colors cursor-pointer';
    $childNavActiveClasses = 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-200';
    $childNavInactiveClasses = 'text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800';
    $iconBaseClasses = 'h-5 w-5 flex-shrink-0 transition-colors';
    $iconActiveClasses = 'text-indigo-600 dark:text-indigo-300';
    $iconInactiveClasses = 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-500 dark:group-hover:text-indigo-200';
    $currentUrl = url()->current();
@endphp

<div x-data="{ openAccordion: null }">
    <!-- Mobile sidebar -->
    <div
        x-show="$store.layout.mobileSidebarOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
        @click="$store.layout.closeMobileSidebar()"
    ></div>

    <aside
        x-show="$store.layout.mobileSidebarOpen"
        x-transition:enter="transform transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-white/95 px-5 py-6 shadow-xl backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 lg:hidden"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <span class="text-lg font-semibold">{{ $brandInitials }}</span>
                </div>
                <span class="text-lg font-semibold text-slate-900 dark:text-white">{{ $brandName }}</span>
            </div>
            <button
                type="button"
                class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                @click="$store.layout.closeMobileSidebar()"
                aria-label="Close sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <nav class="mt-6 flex-1 overflow-y-auto">
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    @php
                        $hasChildren = isset($menu['children']) && is_array($menu['children']);
                        $childActive = false;
                        $menuActivePatterns = $menu['active'] ?? [];

                        if ($hasChildren) {
                            foreach ($menu['children'] as $child) {
                                $childHref = $child['href'] ?? '#';
                                $childActivePatterns = $child['active'] ?? [];
                                $matchesRoute = $childHref !== '#' && $childHref !== '' && $currentUrl === $childHref;
                                $matchesName = !empty($childActivePatterns) && request()->routeIs(...$childActivePatterns);

                                if ($matchesRoute || $matchesName) {
                                    $childActive = true;
                                    break;
                                }
                            }
                        }

                        $href = $menu['href'] ?? '#';
                        $routeMatches = $href !== '#' && $href !== '' && $currentUrl === $href;
                        $patternMatches = !empty($menuActivePatterns) && request()->routeIs(...$menuActivePatterns);

                        $isActive = $hasChildren
                            ? ($childActive || $patternMatches)
                            : ($routeMatches || $patternMatches);
                    @endphp

                    @if ($hasChildren)
                        <div x-data="{ menuId: '{{ $loop->index }}' }" class="space-y-1">
                            <button
                                type="button"
                                class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }} px-4 gap-3 justify-between"
                                @click="openAccordion = openAccordion === menuId ? null : menuId"
                            >
                                <span class="flex items-center gap-3">
                                    <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                                    <span>{{ $menu['label'] }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="openAccordion === menuId ? 'rotate-180' : ''"></i>
                            </button>

                            <div class="space-y-1 pl-11" x-show="openAccordion === menuId" x-transition.opacity x-transition.duration.150ms>
                                @foreach ($menu['children'] as $child)
                                    @php
                                        $childHref = $child['href'] ?? '#';
                                        $childActivePatterns = $child['active'] ?? [];
                                        $childIsActive = ($childHref !== '#' && $childHref !== '' && $currentUrl === $childHref)
                                            || (!empty($childActivePatterns) && request()->routeIs(...$childActivePatterns));
                                    @endphp

                                    <a
                                        href="{{ $childHref }}"
                                        title="{{ $child['label'] }}"
                                        class="{{ $childNavBaseClasses }} {{ $childIsActive ? $childNavActiveClasses : $childNavInactiveClasses }}"
                                        @click="$store.layout.closeMobileSidebar()"
                                        @if($childIsActive) aria-current="page" @endif
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $href }}"
                            title="{{ $menu['label'] }}"
                            class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }} px-4 gap-3 justify-start"
                            @if($isActive) aria-current="page" @endif
                        >
                            <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                            <span>{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>

        <div class="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-800 dark:bg-slate-800/60">
            <p class="font-semibold text-slate-900 dark:text-white" x-show="$store.layout.sidebarExpanded">Sambutan 👋</p>
            <p class="mt-1 leading-relaxed text-slate-600 dark:text-slate-300" x-show="$store.layout.sidebarExpanded">
                Kelola modul Selaju dengan tampilan baru yang konsisten.
            </p>
            <div class="flex items-center justify-center" x-show="!$store.layout.sidebarExpanded">
                <span class="text-lg">👋</span>
            </div>
        </div>
    </aside>

    <aside
        class="fixed inset-y-0 left-0 z-40 hidden h-screen w-20 flex-col border-r border-slate-200 bg-white/90 px-3 py-6 text-sm backdrop-blur lg:flex dark:border-slate-800 dark:bg-slate-900/90"
        :class="{
            'lg:!hidden': !$store.layout.sidebarVisible,
            'lg:w-72 lg:px-5': $store.layout.sidebarExpanded,
            'lg:w-20': !$store.layout.sidebarExpanded
        }"
    >
        <div class="flex items-center justify-between" :class="$store.layout.sidebarExpanded ? '' : 'justify-center'">
            <div class="flex items-center gap-3" :class="$store.layout.sidebarExpanded ? '' : 'justify-center'">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <span class="text-lg font-semibold">{{ $brandInitials }}</span>
                </div>
                <span
                    class="text-lg font-semibold text-slate-900 dark:text-white"
                    x-show="$store.layout.sidebarExpanded"
                    x-transition.opacity
                >{{ $brandName }}</span>
                <span class="sr-only" x-show="!$store.layout.sidebarExpanded">{{ $brandName }}</span>
            </div>

            <button
                type="button"
                class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                @click="$store.layout.toggleSidebar()"
                aria-label="Toggle sidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 010 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <nav class="mt-6 flex-1 overflow-y-auto">
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    @php
                        $hasChildren = isset($menu['children']) && is_array($menu['children']);
                        $childActive = false;
                        $menuActivePatterns = $menu['active'] ?? [];

                        if ($hasChildren) {
                            foreach ($menu['children'] as $child) {
                                $childHref = $child['href'] ?? '#';
                                $childActivePatterns = $child['active'] ?? [];
                                $matchesRoute = $childHref !== '#' && $childHref !== '' && $currentUrl === $childHref;
                                $matchesName = !empty($childActivePatterns) && request()->routeIs(...$childActivePatterns);

                                if ($matchesRoute || $matchesName) {
                                    $childActive = true;
                                    break;
                                }
                            }
                        }

                        $href = $menu['href'] ?? '#';
                        $routeMatches = $href !== '#' && $href !== '' && $currentUrl === $href;
                        $patternMatches = !empty($menuActivePatterns) && request()->routeIs(...$menuActivePatterns);

                        $isActive = $hasChildren
                            ? ($childActive || $patternMatches)
                            : ($routeMatches || $patternMatches);
                    @endphp

                    @if ($hasChildren)
                        <div x-data="{ menuId: '{{ $loop->index }}' }" class="space-y-1">
                            <button
                                type="button"
                                class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }}"
                                :class="$store.layout.sidebarExpanded ? '{{ $navExpandedSpacing }} justify-between' : '{{ $navCollapsedSpacing }}'"
                                @click="
                                    if (!$store.layout.sidebarExpanded) {
                                        $store.layout.sidebarExpanded = true;
                                        $store.layout.persistSidebarState();
                                        openAccordion = menuId;
                                    } else {
                                        openAccordion = openAccordion === menuId ? null : menuId;
                                    }
                                "
                            >
                                <span class="flex items-center gap-3">
                                    <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                                    <span
                                        x-show="$store.layout.sidebarExpanded"
                                        x-transition.opacity
                                    >{{ $menu['label'] }}</span>
                                    <span x-show="!$store.layout.sidebarExpanded" class="sr-only">{{ $menu['label'] }}</span>
                                </span>
                                <i
                                    x-show="$store.layout.sidebarExpanded"
                                    class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                                    :class="openAccordion === menuId ? 'rotate-180' : ''"
                                ></i>
                            </button>

                            <div
                                class="space-y-1"
                                x-show="openAccordion === menuId && $store.layout.sidebarExpanded"
                                x-transition.opacity
                            >
                                @foreach ($menu['children'] as $child)
                                    @php
                                        $childHref = $child['href'] ?? '#';
                                        $childActivePatterns = $child['active'] ?? [];
                                        $childIsActive = ($childHref !== '#' && $childHref !== '' && $currentUrl === $childHref)
                                            || (!empty($childActivePatterns) && request()->routeIs(...$childActivePatterns));
                                    @endphp

                                    <a
                                        href="{{ $childHref }}"
                                        title="{{ $child['label'] }}"
                                        class="ml-11 {{ $childNavBaseClasses }} {{ $childIsActive ? $childNavActiveClasses : $childNavInactiveClasses }}"
                                        @if($childIsActive) aria-current="page" @endif
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $href }}"
                            title="{{ $menu['label'] }}"
                            class="{{ $navBaseClasses }} {{ $isActive ? $navActiveClasses : $navInactiveClasses }}"
                            :class="$store.layout.sidebarExpanded ? '{{ $navExpandedSpacing }} justify-start' : '{{ $navCollapsedSpacing }}'"
                            @if($isActive) aria-current="page" @endif
                        >
                            <x-icon :name="$menu['icon']" class="{{ $iconBaseClasses }} {{ $isActive ? $iconActiveClasses : $iconInactiveClasses }}" />
                            <span
                                x-show="$store.layout.sidebarExpanded"
                                x-transition.opacity
                            >{{ $menu['label'] }}</span>
                            <span x-show="!$store.layout.sidebarExpanded" class="sr-only">{{ $menu['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </nav>


    </aside>
</div>
