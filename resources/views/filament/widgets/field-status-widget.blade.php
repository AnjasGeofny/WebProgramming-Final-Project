<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Status Lapangan Real-time
                </h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ now()->format('d M Y H:i') }}
                </div>
            </div>

            <!-- Today's Summary Stats -->
            @php
                $todayStats = $this->getTodayStats();
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $todayStats['total_bookings'] }}
                    </div>
                    <div class="text-xs text-blue-600 dark:text-blue-400">Total Booking</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $todayStats['completed_bookings'] }}
                    </div>
                    <div class="text-xs text-green-600 dark:text-green-400">Terkonfirmasi</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ $todayStats['pending_bookings'] }}
                    </div>
                    <div class="text-xs text-yellow-600 dark:text-yellow-400">Pending</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        Rp {{ number_format($todayStats['total_revenue'], 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-purple-600 dark:text-purple-400">Pendapatan</div>
                </div>
                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $todayStats['fields_in_use'] }}/{{ $todayStats['total_fields'] }}
                    </div>
                    <div class="text-xs text-indigo-600 dark:text-indigo-400">Lapangan Aktif</div>
                </div>
                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-3">
                    <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                        {{ round(($todayStats['fields_in_use'] / max($todayStats['total_fields'], 1)) * 100, 1) }}%
                    </div>
                    <div class="text-xs text-teal-600 dark:text-teal-400">Utilisasi</div>
                </div>
            </div>

            <!-- Fields Status Grid -->
            @php
                $fieldsData = $this->getFieldsData();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($fieldsData as $field)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 
                    {{ $field['current_status'] === 'Occupied' ? 'bg-red-50 dark:bg-red-900/20' : 
                       ($field['current_status'] === 'Available' ? 'bg-green-50 dark:bg-green-900/20' : 
                        'bg-yellow-50 dark:bg-yellow-900/20') }}">
                    
                    <!-- Field Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                {{ $field['name'] }}
                            </h4>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Court {{ $field['court_number'] }} • {{ $field['type'] }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-medium 
                                {{ $field['current_status'] === 'Occupied' ? 'text-red-600 dark:text-red-400' : 
                                   ($field['current_status'] === 'Available' ? 'text-green-600 dark:text-green-400' : 
                                    'text-yellow-600 dark:text-yellow-400') }}">
                                {{ $field['current_status'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $field['utilization_rate'] }}% today
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    @if($field['current_booking'])
                    <div class="mb-3 p-2 bg-white dark:bg-gray-800 rounded border-l-4 border-red-500">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Sedang digunakan oleh:
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $field['current_booking']['customer'] }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            Sampai {{ Carbon\Carbon::parse($field['current_booking']['until'])->format('H:i') }}
                        </div>
                    </div>
                    @endif

                    <!-- Next Booking -->
                    @if($field['next_booking'])
                    <div class="mb-3 p-2 bg-white dark:bg-gray-800 rounded border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Booking selanjutnya:
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $field['next_booking']['customer'] }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            {{ Carbon\Carbon::parse($field['next_booking']['start_time'])->format('H:i') }} - 
                            {{ Carbon\Carbon::parse($field['next_booking']['end_time'])->format('H:i') }}
                        </div>
                    </div>
                    @endif

                    <!-- Stats Summary -->
                    <div class="flex justify-between items-center text-xs text-gray-600 dark:text-gray-400">
                        <div>
                            <span class="font-medium">{{ $field['booked_slots'] }}</span> booked, 
                            <span class="font-medium">{{ $field['available_slots'] }}</span> available
                        </div>
                        <div>
                            Rp {{ number_format($field['price_per_hour'], 0, ',', '.') }}/jam
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-3 flex gap-2">
                        <a href="{{ \App\Filament\Resources\ScheduleResource::getUrl('index', ['tableFilters[field_id][value]' => $field['id']]) }}" 
                           class="flex-1 text-center px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-xs hover:bg-blue-200 dark:hover:bg-blue-900/50">
                            Lihat Jadwal
                        </a>
                        <a href="{{ \App\Filament\Resources\FieldResource::getUrl('edit', ['record' => $field['id']]) }}" 
                           class="flex-1 text-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded text-xs hover:bg-gray-200 dark:hover:bg-gray-600">
                            Edit
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($fieldsData->isEmpty())
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <div class="text-lg mb-2">📍</div>
                <div>Belum ada lapangan yang terdaftar</div>
                <div class="mt-2">
                    <a href="{{ \App\Filament\Resources\FieldResource::getUrl('create') }}" 
                       class="text-blue-600 dark:text-blue-400 hover:underline">
                        Tambah lapangan pertama
                    </a>
                </div>
            </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
