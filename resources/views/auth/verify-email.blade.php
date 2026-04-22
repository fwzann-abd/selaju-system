<x-guest-layout>
    <x-slot name="heading">Verifikasi Email</x-slot>
    <x-slot name="subheading">Satu langkah lagi sebelum Anda bisa memulai</x-slot>

    <div class="space-y-5">
        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan. Jika belum menerima email tersebut, kami dengan senang hati akan mengirimkan yang baru.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div x-data x-init="setTimeout(() => $dispatch('toast', { code: 200, message: 'Link verifikasi baru telah dikirim ke email Anda.', type: 'success' }), 150)" class="hidden"></div>
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/10 ring-inset dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">Link verifikasi terkirim</span>
        @endif

        <div class="flex items-center justify-between gap-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button>
                    Kirim Ulang Email
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cursor-pointer text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
