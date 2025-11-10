import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('layout', {
    sidebarExpanded: true,
    mobileSidebarOpen: false,
    theme: 'light',

    init() {
        this.sidebarExpanded = JSON.parse(localStorage.getItem('sidebarExpanded') ?? 'true');
        this.persistSidebarState();

        const storedTheme = localStorage.getItem('theme');
        if (storedTheme === 'dark' || storedTheme === 'light') {
            this.theme = storedTheme;
        } else {
            this.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        this.applyTheme();
        this.handleResize();
    },

    toggleSidebar() {
        this.sidebarExpanded = !this.sidebarExpanded;
        this.persistSidebarState();
        this.mobileSidebarOpen = false;
    },

    persistSidebarState() {
        localStorage.setItem('sidebarExpanded', JSON.stringify(this.sidebarExpanded));
    },

    toggleMobileSidebar() {
        if (window.matchMedia('(min-width: 1024px)').matches) {
            this.toggleSidebar();
            return;
        }

        this.mobileSidebarOpen = !this.mobileSidebarOpen;
    },

    closeMobileSidebar() {
        this.mobileSidebarOpen = false;
    },

    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        this.persistTheme();
        this.applyTheme();
    },

    setTheme(theme) {
        if (!['dark', 'light'].includes(theme)) {
            return;
        }

        this.theme = theme;
        this.persistTheme();
        this.applyTheme();
    },

    persistTheme() {
        localStorage.setItem('theme', this.theme);
    },

    applyTheme() {
        const isDark = this.theme === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    },

    handleResize() {
        if (window.matchMedia('(min-width: 1024px)').matches) {
            this.mobileSidebarOpen = false;
        }
    },
});

document.addEventListener('alpine:init', () => {
    const layoutStore = Alpine.store('layout');
    layoutStore.init();
    window.addEventListener('resize', () => layoutStore.handleResize());
});

Alpine.start();
