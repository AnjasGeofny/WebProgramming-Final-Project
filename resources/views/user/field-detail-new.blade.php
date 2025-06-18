@extends('user.layout')

@section('title', $field->name . ' - Booking Detail')

@section('content')
<!-- Field Detail Section -->
<section class="min-h-screen bg-gradient-to-br from-teal-400 via-teal-500 to-cyan-600 py-16">
    <!-- Content Container -->
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('fields') }}" class="inline-flex items-center text-white hover:text-gray-200 transition-colors duration-300">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali ke Daftar Lapangan</span>
            </a>
        </div>

        <!-- Field Info Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-8 py-6">
                <div class="text-center">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                        {{ $field->name }}
                    </h1>
                    <div class="flex items-center justify-center text-teal-100 mb-4">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span class="text-lg">{{ $field->location }}</span>
                    </div>
                </div>
            </div>

            <!-- Sport Type Tabs -->
            <div class="px-8 py-6 border-b border-gray-200">
                <div class="flex justify-center space-x-4">
                    @foreach($courtsByType as $sportType => $courts)
                    <button class="sport-tab px-8 py-3 rounded-full font-medium transition-all duration-300 {{ $loop->first ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}" 
                            data-sport="{{ strtolower($sportType) }}">
                        {{ ucfirst($sportType) }} ({{ $courts->count() }} Lapangan)
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Booking Schedule Grid with Slides -->
            <div class="p-8">
                @foreach($courtsByType as $sportType => $courts)
                <div class="sport-content {{ $loop->first ? 'block' : 'hidden' }}" data-sport="{{ strtolower($sportType) }}">
                    <!-- Sport Type Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Lapangan {{ $sportType }}</h2>
                        <p class="text-gray-600">{{ $courts->count() }} lapangan tersedia dengan harga Rp {{ number_format($courts->first()->price_per_hour, 0, ',', '.') }}/jam</p>
                    </div>

                    <!-- Courts Grid for this sport type -->
                    <div class="space-y-8">
                        @foreach($courts as $court)
                        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                            <!-- Court Header -->
                            <div class="bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Lapangan {{ $court->court_number }}
                                    </h3>
                                    <div class="bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $court->type }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Days Grid for this court -->
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Today for this court -->
                                    <div class="bg-gray-50 rounded-2xl p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-lg font-bold text-gray-900">Hari ini, {{ $today->translatedFormat('d F Y') }}</h4>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <i class="fas fa-calendar-alt text-gray-600"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Time Slots Grid -->
                                        <div class="grid grid-cols-3 gap-2">
                                            @php
                                                $timeSlots = [
                                                    '07:00 - 08:00', '09:00 - 10:00', '11:00 - 12:00',
                                                    '13:00 - 14:00', '15:00 - 16:00', '17:00 - 18:00',
                                                    '19:00 - 20:00', '21:00 - 22:00'
                                                ];
                                                $todayDate = $today->format('Y-m-d');
                                                $todayBookedSlots = $bookedSlots[$court->id][$todayDate] ?? [];
                                            @endphp
                                            
                                            @foreach($timeSlots as $slot)
                                            @php
                                                $isBooked = in_array($slot, $todayBookedSlots);
                                            @endphp
                                            <button class="time-slot p-3 rounded-lg text-sm font-medium transition-all duration-300 
                                                {{ $isBooked ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white' }}"
                                                {{ $isBooked ? 'disabled' : '' }}
                                                data-court-id="{{ $court->id }}"
                                                data-court-number="{{ $court->court_number }}"
                                                data-sport-type="{{ $court->type }}"
                                                data-date="{{ $todayDate }}"
                                                data-time="{{ $slot }}">
                                                {{ $slot }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Tomorrow for this court -->
                                    <div class="bg-gray-50 rounded-2xl p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $tomorrow->translatedFormat('d F Y') }}</h4>
                                            <div class="bg-white rounded-lg p-2 shadow-sm">
                                                <i class="fas fa-calendar-alt text-gray-600"></i>
                                            </div>
                                        </div>
                                        
                                        <!-- Time Slots Grid -->
                                        <div class="grid grid-cols-3 gap-2">
                                            @php
                                                $tomorrowDate = $tomorrow->format('Y-m-d');
                                                $tomorrowBookedSlots = $bookedSlots[$court->id][$tomorrowDate] ?? [];
                                            @endphp
                                            
                                            @foreach($timeSlots as $slot)
                                            @php
                                                $isBooked = in_array($slot, $tomorrowBookedSlots);
                                            @endphp
                                            <button class="time-slot p-3 rounded-lg text-sm font-medium transition-all duration-300 
                                                {{ $isBooked ? 'bg-red-500 text-white cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white' }}"
                                                {{ $isBooked ? 'disabled' : '' }}
                                                data-court-id="{{ $court->id }}"
                                                data-court-number="{{ $court->court_number }}"
                                                data-sport-type="{{ $court->type }}"
                                                data-date="{{ $tomorrowDate }}"
                                                data-time="{{ $slot }}">
                                                {{ $slot }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <!-- Legend -->
                <div class="mt-8 flex justify-center items-center space-x-8">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        <span class="text-gray-700 text-sm">Tersedia</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                        <span class="text-gray-700 text-sm">Sudah Dibooking</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Confirmation Modal - DISABLED (functionality removed) -->
<!--
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Konfirmasi Booking</h3>
            <div class="space-y-3 mb-6">
                <p><span class="font-medium">Venue:</span> {{ $field->name }}</p>
                <p><span class="font-medium">Jenis Olahraga:</span> <span id="selectedSportType"></span></p>
                <p><span class="font-medium">Lapangan:</span> <span id="selectedCourt"></span></p>
                <p><span class="font-medium">Tanggal:</span> <span id="selectedDate"></span></p>
                <p><span class="font-medium">Waktu:</span> <span id="selectedTime"></span></p>
                <p><span class="font-medium">Harga:</span> Rp <span id="selectedPrice"></span></p>
            </div>
            <div class="flex space-x-4">
                <button id="confirmBooking" class="flex-1 bg-teal-600 text-white py-3 rounded-lg font-medium hover:bg-teal-700 transition-colors">
                    Konfirmasi Booking
                </button>
                <button id="cancelBooking" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const sportTabs = document.querySelectorAll('.sport-tab');
    const sportContents = document.querySelectorAll('.sport-content');
    
    sportTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetSport = this.getAttribute('data-sport');
            
            // Update tab active state
            sportTabs.forEach(t => {
                t.classList.remove('bg-teal-600', 'text-white');
                t.classList.add('bg-gray-200', 'text-gray-700');
            });
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-teal-600', 'text-white');
            
            // Show corresponding content
            sportContents.forEach(content => {
                if (content.getAttribute('data-sport') === targetSport) {
                    content.classList.remove('hidden');
                    content.classList.add('block');
                } else {
                    content.classList.remove('block');
                    content.classList.add('hidden');
                }
            });
        });
    });    // Booking functionality - DISABLED
    const timeSlots = document.querySelectorAll('.time-slot');
    // const bookingModal = document.getElementById('bookingModal'); // Disabled
    // const confirmBookingBtn = document.getElementById('confirmBooking'); // Disabled
    // const cancelBookingBtn = document.getElementById('cancelBooking'); // Disabled
    
    // let selectedBookingData = {}; // Disabled
      timeSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            if (this.disabled) return;
            
            // Temporary visual feedback - flash blue and return to green
            const originalClasses = Array.from(this.classList);
            
            // Add blue highlight temporarily
            this.classList.remove('bg-green-500');
            this.classList.add('bg-blue-600', 'ring-4', 'ring-blue-300');
            
            // Return to original state after a short delay
            setTimeout(() => {
                this.classList.remove('bg-blue-600', 'ring-4', 'ring-blue-300');
                if (!this.disabled && !this.classList.contains('bg-red-500') && !this.classList.contains('bg-gray-300')) {
                    this.classList.add('bg-green-500');
                }
            }, 300); // 300ms flash effect
            
            // No action - booking functionality disabled
            // User gets brief visual feedback but no persistent selection
        });
        
        // Hover effects
        slot.addEventListener('mouseenter', function() {
            if (!this.disabled && !this.classList.contains('bg-blue-600')) {
                this.classList.add('transform', 'scale-105');
            }
        });
        
        slot.addEventListener('mouseleave', function() {
            if (!this.disabled && !this.classList.contains('bg-blue-600')) {
                this.classList.remove('transform', 'scale-105');
            }
        });
    });
    
    // Modal event handlers
    cancelBookingBtn.addEventListener('click', function() {
        bookingModal.classList.add('hidden');
        // Reset slot selection        timeSlots.forEach(s => {
            s.classList.remove('bg-blue-600', 'ring-4', 'ring-blue-300');
            if (!s.disabled) {
                s.classList.add('bg-green-500');
            }
        });
    });
    
    // Booking event handlers - DISABLED
    /*
    confirmBookingBtn.addEventListener('click', function() {
        // Here you would normally send the booking data to the server
        alert('Booking berhasil! (Simulasi - belum terintegrasi dengan backend)');
        bookingModal.classList.add('hidden');
        
        // Mark the slot as booked (for demo purposes)
        const selectedSlot = document.querySelector('.bg-blue-600');
        if (selectedSlot) {
            selectedSlot.classList.remove('bg-blue-600', 'bg-green-500', 'ring-4', 'ring-blue-300');
            selectedSlot.classList.add('bg-red-500', 'cursor-not-allowed');
            selectedSlot.disabled = true;
            selectedSlot.textContent = selectedBookingData.time;
        }
    });
    */
    
    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
});
</script>

@endsection
