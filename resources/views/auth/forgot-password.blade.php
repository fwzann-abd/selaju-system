<x-guest-layout>
    <x-slot name="heading">Lupa Password</x-slot>
    <x-slot name="subheading">Kami akan kirimkan link reset password ke email Anda</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1.5">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email Anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Kirim Link Reset Password
        </x-primary-button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="font-medium text-blue-600 transition-colors hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Kembali ke login</a>
        </p>
    </form>
</x-guest-layout>
