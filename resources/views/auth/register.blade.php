<x-guest-layout>
    <x-slot name="heading">Daftar Akun</x-slot>
    <x-slot name="subheading">Buat akun Selaju System Anda</x-slot>

    <div x-data="registerFlow()" @keydown.escape="step = 1" class="w-full">

        <!-- Step Indicator -->
        <div class="flex items-center justify-center gap-3 mb-6" x-show="step < 3">
            <template x-for="s in [1, 2]" :key="s">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold transition-all duration-300"
                         :class="step === s ? 'bg-[#136dec] text-white shadow-lg shadow-blue-500/30' : (step > s ? 'bg-green-500/20 text-green-400' : 'bg-white/10 text-slate-500')">
                        <span x-show="step <= s" x-text="s"></span>
                        <svg x-show="step > s" x-cloak class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <template x-if="s < 2">
                        <div class="w-12 h-px transition-colors" :class="step > s ? 'bg-green-500/40' : 'bg-white/10'"></div>
                    </template>
                </div>
            </template>
        </div>

        <!-- General Error -->
        <div x-show="errors.general" x-cloak class="mb-4 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400">
            <span x-text="errors.general"></span>
        </div>

        <!-- Step 1: Register Form -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
            <form @submit.prevent="submitRegister" class="space-y-4">
                <!-- Username -->
                <div class="space-y-1.5">
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" x-model="form.username" type="text" name="username" required autofocus autocomplete="off" placeholder="username" />
                    <span x-show="errors.username" x-cloak class="text-red-400 text-xs" x-text="errors.username"></span>
                </div>

                <!-- Name -->
                <div class="space-y-1.5">
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" x-model="form.name" type="text" name="name" required autocomplete="name" placeholder="Nama lengkap Anda" />
                    <span x-show="errors.name" x-cloak class="text-red-400 text-xs" x-text="errors.name"></span>
                </div>

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" x-model="form.email" type="email" name="email" required autocomplete="email" placeholder="email@example.com" />
                    <span x-show="errors.email" x-cloak class="text-red-400 text-xs" x-text="errors.email"></span>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" x-model="form.password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <span x-show="errors.password" x-cloak class="text-red-400 text-xs" x-text="errors.password"></span>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5">
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" x-model="form.password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('login') }}" class="text-sm text-sky-400 hover:text-sky-300 transition-colors">
                        Sudah punya akun?
                    </a>
                    <x-primary-button type="submit" x-bind:disabled="loading">
                        <span x-show="!loading">Lanjut</span>
                        <span x-show="loading" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- Step 2: School Selection -->
        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-4">
            <p class="text-sm text-slate-400">Pilih sekolah Anda (opsional)</p>

            <div x-show="!loadingSchools" class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                <template x-for="school in schools" :key="school.id">
                    <button @click="selectedSchool = school.id; submitSchool()"
                            class="w-full p-3 text-left rounded-xl bg-white/5 border border-white/10 hover:bg-[#136dec]/10 hover:border-[#136dec]/30 transition-all duration-200 group">
                        <div class="font-semibold text-white text-sm group-hover:text-sky-300 transition-colors" x-text="school.name"></div>
                        <div class="text-xs text-slate-500 mt-0.5" x-text="'/' + school.slug"></div>
                    </button>
                </template>
            </div>

            <div x-show="loadingSchools" x-cloak class="flex items-center justify-center py-8 gap-2 text-slate-400">
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm">Memuat sekolah...</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button @click="step = 1" class="flex-1 px-4 py-2.5 text-sm text-slate-400 border border-white/10 rounded-xl hover:bg-white/5 transition-all">
                    Kembali
                </button>
                <button @click="skipSchool()" class="flex-1 px-4 py-2.5 text-sm text-slate-300 border border-white/10 rounded-xl hover:bg-white/5 transition-all">
                    Lewati
                </button>
            </div>
        </div>

        <!-- Step 3: Success -->
        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="space-y-6 text-center py-4">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-500/15 ring-4 ring-green-500/10">
                <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white mb-2">Pendaftaran Berhasil!</h3>
                <p class="text-sm text-slate-400">Akun Anda telah berhasil dibuat. Silakan login untuk melanjutkan.</p>
            </div>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#136dec] text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-500/25 hover:bg-[#1a7fff] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Ke Halaman Login
            </a>
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('registerFlow', () => ({
                        step: 1,
                        loading: false,
                        loadingSchools: false,
                        schools: [],
                        selectedSchool: null,
                        participantId: null,
                        form: {
                            username: '',
                            name: '',
                            email: '',
                            password: '',
                            password_confirmation: '',
                        },
                        errors: {},

                        async submitRegister() {
                            this.loading = true;
                            this.errors = {};

                            try {
                                const response = await fetch('/api/register', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    },
                                    body: JSON.stringify(this.form),
                                });

                                if (!response.ok) {
                                    const data = await response.json();
                                    this.errors = data.errors || {};
                                    return;
                                }

                                const data = await response.json();
                                this.participantId = data.participant.id;
                                this.step = 2;
                                await this.loadSchools();
                            } catch (error) {
                                console.error('Error registering:', error);
                                this.errors.general = 'Terjadi kesalahan. Silakan coba lagi.';
                            } finally {
                                this.loading = false;
                            }
                        },

                        async loadSchools() {
                            this.loadingSchools = true;

                            try {
                                const response = await fetch('/api/schools');
                                const data = await response.json();
                                this.schools = data.data || [];
                            } catch (error) {
                                console.error('Error loading schools:', error);
                                this.schools = [];
                            } finally {
                                this.loadingSchools = false;
                            }
                        },

                        async submitSchool() {
                            try {
                                const response = await fetch(`/api/register/${this.participantId}/school`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    },
                                    body: JSON.stringify({
                                        school_id: this.selectedSchool,
                                    }),
                                });

                                if (response.ok) {
                                    this.step = 3;
                                }
                            } catch (error) {
                                console.error('Error updating school:', error);
                            }
                        },

                        skipSchool() {
                            this.step = 3;
                        },
                    }))
                })
            </script>
        @endpush
    @endonce
</x-guest-layout>
