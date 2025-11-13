<x-guest-layout>
    <div x-data="registerFlow()"
         @keydown.escape="step = 1"
         class="w-full max-w-md mx-auto">

        <!-- Step 1: Register Form -->
        <div x-show="step === 1" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Akun Selaju</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Langkah 1: Buat akun Anda</p>

            <form @submit.prevent="submitRegister" class="space-y-4">
                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" x-model="form.username" type="text" name="username" required autofocus autocomplete="off" placeholder="username" />
                    <span x-show="errors.username" class="text-red-500 text-xs mt-1" x-text="errors.username"></span>
                </div>

                <!-- Name -->
                <div>
                    <x-input-label for="name" value="Nama Lengkap" />
                    <x-text-input id="name" x-model="form.name" type="text" name="name" required autocomplete="name" placeholder="Nama Anda" />
                    <span x-show="errors.name" class="text-red-500 text-xs mt-1" x-text="errors.name"></span>
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" x-model="form.email" type="email" name="email" required autocomplete="email" placeholder="email@example.com" />
                    <span x-show="errors.email" class="text-red-500 text-xs mt-1" x-text="errors.email"></span>
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" x-model="form.password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <span x-show="errors.password" class="text-red-500 text-xs mt-1" x-text="errors.password"></span>
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" x-model="form.password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        Sudah punya akun?
                    </a>
                    <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                        <span x-show="!loading">Lanjut</span>
                        <span x-show="loading">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: School Selection -->
        <div x-show="step === 2" class="space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pilih Sekolah</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Langkah 2: Pilih sekolah Anda (opsional, bisa dilewati)</p>

            <div x-show="!loadingSchools" class="space-y-2 max-h-96 overflow-y-auto">
                <template x-for="school in schools" :key="school.id">
                    <button @click="selectedSchool = school.id; submitSchool()"
                            class="w-full p-3 text-left border border-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 dark:border-gray-600 transition">
                        <div class="font-semibold text-gray-900 dark:text-white" x-text="school.name"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="'/' + school.slug"></div>
                    </button>
                </template>
            </div>

            <div x-show="loadingSchools" class="flex justify-center">
                <span class="text-gray-500 dark:text-gray-400">Memuat sekolah...</span>
            </div>

            <div class="flex gap-3">
                <button @click="skipSchool()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                    Lewati
                </button>
            </div>
        </div>

        <!-- Step 3: Success -->
        <div x-show="step === 3" class="space-y-6 text-center">
            <div class="inline-block p-4 bg-green-100 dark:bg-green-900/30 rounded-full">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pendaftaran Berhasil!</h2>
            <p class="text-gray-600 dark:text-gray-400">Akun Anda telah berhasil dibuat. Silakan login untuk melanjutkan.</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
                                this.participantId = data.participant.uuid;
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
