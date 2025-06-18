<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Live Booking Dashboard
                </h3>
                <div class="flex gap-2">
                    <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}" 
                       class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                        Lihat Semua
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Today's Bookings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                        Booking Hari Ini
                    </h4>
                    
                    @php
                        $todayBookings = $this->getTodayBookings();
                    @endphp
                    
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($todayBookings as $booking)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $booking['customer_name'] }}
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $booking['session_status'] === 'ongoing' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                           ($booking['session_status'] === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                        {{ $booking['session_status'] === 'ongoing' ? 'Sedang Bermain' : 
                                           ($booking['session_status'] === 'completed' ? 'Selesai' : 'Akan Datang') }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $booking['field_name'] }} Court {{ $booking['court_number'] }} • {{ $booking['schedule_time'] }}
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-500">
                                        Rp {{ number_format($booking['total_price'], 0, ',', '.') }}
                                    </span>
                                    <span class="px-1.5 py-0.5 text-xs rounded
                                        {{ $booking['payment_status'] === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 
                                           'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' }}">
                                        {{ $booking['payment_status'] === 'paid' ? 'Lunas' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <a href="{{ \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $booking['id']]) }}" 
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            Belum ada booking hari ini
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Upcoming Schedules -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Jadwal Berikutnya
                    </h4>
                    
                    @php
                        $upcomingSchedules = $this->getUpcomingSchedules();
                    @endphp
                    
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($upcomingSchedules as $schedule)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $schedule['field_name'] }} Court {{ $schedule['court_number'] }}
                                    </div>
                                    @if($schedule['time_until'])
                                    <span class="text-xs text-blue-600 dark:text-blue-400">
                                        dalam {{ $schedule['time_until'] }}
                                    </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $schedule['start_time'] }} - {{ $schedule['end_time'] }} • {{ $schedule['field_type'] }}
                                </div>
                                <div class="flex items-center gap-3 mt-1">
                                    @if($schedule['is_booked'])
                                        <span class="text-xs text-gray-900 dark:text-gray-100">
                                            {{ $schedule['customer_name'] }}
                                        </span>
                                        <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400 rounded">
                                            Terboking
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-500 dark:text-gray-500">
                                            Rp {{ number_format($schedule['price'], 0, ',', '.') }}/jam
                                        </span>
                                        <span class="px-1.5 py-0.5 text-xs bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded">
                                            {{ $schedule['is_available'] ? 'Tersedia' : 'Maintenance' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            Tidak ada jadwal berikutnya hari ini
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pending Actions Section -->
            @php
                $pendingActions = $this->getPendingActions();
            @endphp
            
            @if($pendingActions['pending_bookings']->count() > 0 || $pendingActions['pending_payments']->count() > 0)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                    Memerlukan Tindakan
                    <span class="ml-2 px-2 py-1 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs rounded-full">
                        {{ $pendingActions['pending_bookings']->count() + $pendingActions['pending_payments']->count() }}
                    </span>
                </h4>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Pending Bookings -->
                    @if($pendingActions['pending_bookings']->count() > 0)
                    <div class="space-y-2">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Booking Menunggu Konfirmasi ({{ $pendingActions['pending_bookings']->count() }})
                        </h5>
                        @foreach($pendingActions['pending_bookings'] as $booking)
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $booking['customer_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $booking['field_name'] }} • {{ $booking['schedule_date'] }} {{ $booking['schedule_time'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $booking['created_at'] }} • Rp {{ number_format($booking['total_price'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <a href="{{ \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $booking['id']]) }}" 
                                   class="px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                                    Review
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Pending Payments -->
                    @if($pendingActions['pending_payments']->count() > 0)
                    <div class="space-y-2">
                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Pembayaran Menunggu Verifikasi ({{ $pendingActions['pending_payments']->count() }})
                        </h5>
                        @foreach($pendingActions['pending_payments'] as $payment)
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $payment['customer_name'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $payment['field_name'] }} • {{ $payment['payment_method'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $payment['created_at'] }} • Rp {{ number_format($payment['total_price'], 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    @if($payment['proof_url'])
                                    <a href="{{ $payment['proof_url'] }}" target="_blank"
                                       class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700">
                                        Bukti
                                    </a>                                    @endif
                                    <a href="{{ \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $payment['booking_id']]) }}" 
                                       class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        Verifikasi Booking
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recent Activity Summary -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                @php
                    $recentBookings = $this->getRecentBookings(5);
                @endphp
                
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                        Aktivitas Terbaru
                    </h4>
                    <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua
                    </a>
                </div>
                
                <div class="space-y-2">
                    @forelse($recentBookings as $booking)
                    <div class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                <span class="font-medium">{{ $booking['customer_name'] }}</span> 
                                booking {{ $booking['field_name'] }} Court {{ $booking['court_number'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $booking['schedule_date'] }} {{ $booking['schedule_time'] }} • {{ $booking['created_at'] }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">                            <span class="px-2 py-1 text-xs rounded
                                {{ $booking['status'] === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 
                                   ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' : 
                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}">>
                                {{ ucfirst($booking['status']) }}
                            </span>
                            @if($booking['is_today'])
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded">
                                Hari ini
                            </span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        Belum ada aktivitas terbaru
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
