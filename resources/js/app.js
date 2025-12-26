import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.store('layout', {
    sidebarExpanded: true,
    sidebarVisible: true,
    mobileSidebarOpen: false,
    theme: 'light',

    init() {
        this.sidebarExpanded = JSON.parse(localStorage.getItem('sidebarExpanded') ?? 'true');
        this.sidebarVisible = JSON.parse(localStorage.getItem('sidebarVisible') ?? 'true');
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

    hideSidebar() {
        this.sidebarVisible = false;
        this.persistSidebarState();
        this.mobileSidebarOpen = false;
    },

    showSidebar() {
        this.sidebarVisible = true;
        this.persistSidebarState();
    },

    toggleSidebarVisibility() {
        this.sidebarVisible = !this.sidebarVisible;
        this.persistSidebarState();
        this.mobileSidebarOpen = false;
    },

    persistSidebarState() {
        localStorage.setItem('sidebarExpanded', JSON.stringify(this.sidebarExpanded));
        localStorage.setItem('sidebarVisible', JSON.stringify(this.sidebarVisible));
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

    Alpine.data('studentImportComponent', (config = {}) => ({
        isUploading: false,
        isPreviewing: false,
        filename: '',
        schoolId: config.initialSchool || '',
        previewRows: [],
        previewPage: 1,
        perPage: 10,
        previewErrors: [],
        importToken: '',
        summary: { valid: 0, invalid: 0 },
        previewUrl: config.previewUrl || '',

        get totalPages() {
            return this.previewRows.length ? Math.ceil(this.previewRows.length / this.perPage) : 1;
        },

        get paginatedRows() {
            const start = (this.previewPage - 1) * this.perPage;
            return this.previewRows.slice(start, start + this.perPage);
        },

        get resolvedPreviewUrl() {
            if (this.previewUrl) {
                return this.previewUrl;
            }
            if (this.$el?.dataset?.previewUrl) {
                return this.$el.dataset.previewUrl;
            }
            return '';
        },

        onFileChange(event) {
            const file = event.target.files[0];
            this.filename = file ? file.name : '';
            this.resetPreviewState();
        },

        resetPreviewState(clearErrors = true) {
            this.importToken = '';
            this.previewRows = [];
            this.previewPage = 1;
            this.summary = { valid: 0, invalid: 0 };
            if (clearErrors) {
                this.previewErrors = [];
            }
        },

        nextPage() {
            if (this.previewPage < this.totalPages) {
                this.previewPage += 1;
            }
        },

        prevPage() {
            if (this.previewPage > 1) {
                this.previewPage -= 1;
            }
        },

        showAlert({ title = 'Informasi', text = '', icon = 'info' } = {}) {
            if (window.Swal) {
                Swal.fire({ title, text, icon, confirmButtonColor: '#6366F1' });
            } else if (text) {
                alert(text);
            }
        },

        async startPreview() {
            const fileInput = this.$refs.fileInput;
            if (!this.schoolId) {
                this.showAlert({ icon: 'warning', text: 'Pilih sekolah terlebih dahulu.' });
                return;
            }
            if (!fileInput || !fileInput.files.length) {
                this.showAlert({ icon: 'warning', text: 'Pilih file Excel terlebih dahulu.' });
                return;
            }

            const previewUrl = this.resolvedPreviewUrl;
            if (!previewUrl) {
                this.showAlert({ icon: 'error', text: 'URL preview tidak ditemukan.' });
                return;
            }

            this.isPreviewing = true;
            this.previewErrors = [];
            this.previewRows = [];
            this.importToken = '';
            this.previewPage = 1;

            const formData = new FormData();
            formData.append('school_id', this.schoolId);
            formData.append('generation_id', document.querySelector('input[name="generation_id"]')?.value || '');
            formData.append('file', fileInput.files[0]);

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(previewUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                    if (!response.ok) {
                        this.resetPreviewState(false);
                        this.previewErrors = data.errors ?? (data.message ? [data.message] : []);
                        this.showAlert({ icon: 'error', text: data.message || 'Gagal memproses file.' });
                        return;
                    }

                this.previewRows = data.rows ?? [];
                this.previewErrors = data.errors ?? [];
                this.summary = data.summary ?? { valid: this.previewRows.length, invalid: this.previewErrors.length };
                this.importToken = data.token || '';
                this.filename = data.filename || this.filename;
                this.previewPage = 1;

                if (!this.previewRows.length) {
                    this.showAlert({ icon: 'warning', text: 'Tidak ada data yang dapat ditampilkan.' });
                }
            } catch (error) {
                console.error(error);
                this.showAlert({ icon: 'error', text: 'Terjadi kesalahan saat memproses file.' });
                this.resetPreviewState();
            } finally {
                this.isPreviewing = false;
            }
        },

        async onSubmit(event) {
            if (!this.importToken) {
                event.preventDefault();
                this.showAlert({ icon: 'warning', text: 'Silakan lakukan import (preview) terlebih dahulu.' });
                return;
            }

            this.isUploading = true;

            // Reset the uploading state after form submission
            // The form will naturally redirect on success
            setTimeout(() => {
                this.isUploading = false;
            }, 1000);
        },
    }));
});

Alpine.start();
