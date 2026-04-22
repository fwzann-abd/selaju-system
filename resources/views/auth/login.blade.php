<x-guest-layout>
    <x-slot name="heading">Masuk ke Akun</x-slot>
    <x-slot name="subheading">Silakan masukkan kredensial Anda</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" value="Email" />
            <div class="relative">
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="masukkan email Anda" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2" x-data="{ showPassword: false }">
            <x-input-label for="password" value="Password" />
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                    class="block w-full px-4 py-3 bg-white/5 border border-white/10 text-white placeholder-slate-500 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="masukkan password Anda">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <button type="button" @click="showPassword = !showPassword" class="text-slate-500 hover:text-slate-300 focus:outline-none transition-colors">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-white/20 bg-white/5 text-[#136dec] focus:ring-2 focus:ring-blue-500 focus:ring-offset-0">
                <span class="ml-2 text-sm text-slate-400">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-sky-400 hover:text-sky-300 transition-colors duration-200">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <x-primary-button class="w-full justify-center py-3 text-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk
        </x-primary-button>

        <!-- Register Link -->
        @if (Route::has('register'))
            <p class="text-center text-sm text-slate-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-sky-400 hover:text-sky-300 transition-colors">Daftar sekarang</a>
            </p>
        @endif
    </form>
</x-guest-layout>
