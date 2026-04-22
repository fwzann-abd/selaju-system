<x-guest-layout>
    <x-slot name="heading">Konfirmasi Password</x-slot>
    <x-slot name="subheading">Area ini memerlukan konfirmasi password sebelum melanjutkan</x-slot>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div class="space-y-1.5">
            <x-input-label for="password" value="Password" />
            <x-password-input id="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <x-primary-button class="w-full justify-center">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
