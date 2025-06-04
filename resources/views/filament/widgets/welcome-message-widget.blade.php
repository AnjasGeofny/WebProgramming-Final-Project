<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
                Selamat Datang Kembali, {{ $this->userName }}!
            </h2>

            @if($this->userRole)
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Anda login sebagai <span class="font-medium">{{ $this->userRole }}</span>.
            </p>
            @endif

            <p class="mt-3 text-gray-700 dark:text-gray-300">
                Ini adalah dashboard sistem booking lapangan Anda. Gunakan menu navigasi di samping untuk mengelola data booking, lapangan, jadwal, pengguna, dan pembayaran.
            </p>

            {{-- Contoh Tombol Aksi (opsional) --}}
            <div class="mt-6">
                <x-filament::button
                    href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}"
                    tag="a"
                    icon="heroicon-m-calendar-days"
                    color="primary">
                    Lihat Semua Booking
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>