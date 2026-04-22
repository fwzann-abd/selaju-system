<x-guest-layout>
    <x-slot name="heading">Verifikasi Email</x-slot>
    <x-slot name="subheading">Satu langkah lagi sebelum Anda bisa memulai</x-slot>

    <div class="space-y-5">
        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan. Jika belum menerima email tersebut, kami dengan senang hati akan mengirimkan yang baru.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-600 dark:border-green-500/20 dark:bg-green-500/10 dark:text-green-400">
                Link verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
            </div>
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
