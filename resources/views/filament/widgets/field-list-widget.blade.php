<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Field Management Overview</h3>
        <div class="flex space-x-2">
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                Available
            </span>
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                Moderate
            </span>
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                <span class="w-2 h-2 bg-red-400 rounded-full mr-1"></span>
                Busy
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($fields as $field)
            <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                <!-- Field Image -->
                <div class="h-32 bg-gray-100 flex items-center justify-center">
                    @if($field['image'])
                        <img src="{{ asset('images/' . $field['image']) }}" 
                             alt="{{ $field['name'] }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Field Info -->
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-gray-900">{{ $field['name'] }}</h4>
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                            @if($field['status'] === 'available') bg-green-100 text-green-800
                            @elseif($field['status'] === 'moderate') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            <span class="w-2 h-2 rounded-full mr-1
                                @if($field['status'] === 'available') bg-green-400
                                @elseif($field['status'] === 'moderate') bg-yellow-400
                                @else bg-red-400 @endif"></span>
                            {{ ucfirst($field['status']) }}
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-3">
                        <p class="flex justify-between">
                            <span>Type:</span>
                            <span class="font-medium">{{ ucfirst($field['type']) }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span>Price/Hour:</span>
                            <span class="font-medium text-green-600">Rp {{ number_format($field['price_per_hour'], 0, ',', '.') }}</span>
                        </p>
                        <p class="flex justify-between">
                            <span>Today's Utilization:</span>
                            <span class="font-medium">{{ $field['utilization'] }}%</span>
                        </p>
                        <p class="flex justify-between">
                            <span>Booked/Total Slots:</span>
                            <span class="font-medium">{{ $field['booked_schedules'] }}/{{ $field['today_schedules'] }}</span>
                        </p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Utilization Rate</span>
                            <span>{{ $field['utilization'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300
                                @if($field['utilization'] <= 50) bg-green-500
                                @elseif($field['utilization'] <= 80) bg-yellow-500
                                @else bg-red-500 @endif" 
                                style="width: {{ $field['utilization'] }}%"></div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    @if($field['recent_bookings']->count() > 0)
                        <div class="border-t pt-3">
                            <h5 class="text-xs font-medium text-gray-700 mb-2">Recent Bookings:</h5>                            <div class="space-y-1">
                                @foreach($field['recent_bookings'] as $booking)
                                    <div class="text-xs text-gray-600 flex justify-between">
                                        <span class="truncate">{{ $booking->customer_name ?? ($booking->user?->name ?? 'Unknown') }}</span>
                                        <span class="text-gray-500">{{ $booking->created_at->format('M d') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="flex space-x-2 mt-4">
                        <a href="{{ url('/admin/fields/' . $field['id'] . '/edit') }}" 
                           class="flex-1 bg-blue-50 text-blue-700 text-xs px-3 py-2 rounded-md text-center hover:bg-blue-100 transition-colors">
                            Edit Field
                        </a>
                        <a href="{{ url('/admin/schedules?field_id=' . $field['id']) }}" 
                           class="flex-1 bg-green-50 text-green-700 text-xs px-3 py-2 rounded-md text-center hover:bg-green-100 transition-colors">
                            View Schedules
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Auto-refresh indicator -->
    <div class="flex items-center justify-center mt-6 text-xs text-gray-500">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Data updates every 30 seconds
    </div>
</div>

<script>
    // Auto-refresh setiap 30 detik
    setInterval(function() {
        if (typeof Livewire !== 'undefined') {
            Livewire.emit('refreshWidget');
        } else {
            // Fallback refresh untuk non-Livewire
            location.reload();
        }
    }, 30000);
</script>
