<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $field->name }} - Booking Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">    <style>
        .calendar-day {
            @apply w-8 h-8 flex items-center justify-center text-sm rounded-lg transition-all duration-200;
            min-height: 32px;
            min-width: 32px;
            cursor: default;
            user-select: none;
        }
        .calendar-day:not(.disabled) {
            cursor: pointer !important;
        }
        .calendar-day:hover:not(.disabled) {
            @apply bg-gray-100 transform scale-110;
            cursor: pointer !important;
        }
        .calendar-day.today {
            @apply bg-teal-100 text-teal-700 font-medium ring-2 ring-teal-300;
            cursor: pointer !important;
        }
        .calendar-day.selected {
            @apply bg-teal-600 text-white font-medium ring-2 ring-teal-400;
            cursor: pointer !important;
        }
        .calendar-day.disabled {
            @apply text-gray-300;
            cursor: not-allowed !important;
        }
        .calendar-day.disabled:hover {
            @apply bg-transparent transform-none;
            cursor: not-allowed !important;
        }
        .date-display {
            transition: all 0.3s ease;
        }
        .calendar-day:active:not(.disabled) {
            @apply transform scale-95;
            cursor: pointer !important;
        }
        .calendar-day.clickable {
            cursor: pointer !important;
        }
        .calendar-day.clickable:hover {
            @apply bg-gray-100 transform scale-110;
        }
    </style>
</head>
<body class="h-screen overflow-hidden">
    <!-- Full Screen Container -->
    <div class="h-screen flex flex-col bg-white">
        <!-- Back Button - Top Left -->
        <div class="absolute top-6 left-6 z-50">
            <a href="{{ route('fields') }}" class="bg-gray-100 hover:bg-gray-200 transition-all duration-300 p-3 rounded-full shadow-lg">
                <i class="fas fa-arrow-left text-gray-700 text-xl"></i>
            </a>
        </div>

        <!-- Header with Venue Name -->
        <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-8 text-center text-white">
            <h1 class="text-3xl font-bold mb-2">{{ $field->name }}</h1>
        </div>

        <!-- Scrollable Content Area -->
        <div class="flex-1 overflow-y-auto">
            <!-- Location Section -->
            <div class="px-6 py-4 bg-white border-b border-gray-200">
                <div class="bg-gray-100 rounded-2xl p-4">
                    <div class="flex items-center">
                        <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                            <i class="fas fa-map-marker-alt text-gray-600"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 font-medium">Lokasi</div>
                            <div class="text-gray-900 font-medium">{{ $field->location }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sport Type Tabs -->
            @if($courtsByType->count() > 1)
            <div class="px-6 py-4 bg-white">
                <div class="flex justify-center space-x-4">
                    @foreach($courtsByType as $sportType => $courts)
                    <button class="sport-tab px-8 py-3 rounded-full font-medium transition-all duration-300 {{ $loop->first ? 'bg-teal-600 text-white' : 'bg-gray-300 text-gray-700 hover:bg-gray-400' }}"
                             data-sport="{{ strtolower($sportType) }}">
                        {{ ucfirst($sportType) }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Courts Content -->
            @foreach($courtsByType as $sportType => $courts)
            <div class="sport-content {{ $loop->first ? 'block' : 'hidden' }}" data-sport="{{ strtolower($sportType) }}">
                <!-- Courts Grid -->
                <div class="p-6 bg-gray-50">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($courts as $court)
                        <div class="bg-white rounded-2xl p-4 shadow-sm" data-court-id="{{ $court->id }}">
                            <!-- Court Header -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Lapangan {{ $court->court_number }}</h3>
                                <button class="calendar-btn p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors" 
                                        data-court-id="{{ $court->id }}"
                                        data-court-number="{{ $court->court_number }}">
                                    <i class="fas fa-calendar-alt text-gray-600"></i>
                                </button>
                            </div>
                            
                            <div class="date-display text-sm text-gray-600 mb-4" data-court-id="{{ $court->id }}">
                                {{ $selectedDate->translatedFormat('l, d F Y') }}
                            </div>                            <!-- Time Slots Grid -->
                            <div class="time-slots-container" data-court-id="{{ $court->id }}">
                                @php
                                    // Use ONLY dynamic time slots from admin panel - no fallback
                                    $courtAvailableSlots = $availableTimeSlots[$court->id] ?? [];
                                    $timeSlots = $courtAvailableSlots; // Remove fallback
                                    $selectedDateStr = $selectedDate->format('Y-m-d');
                                    $courtBookedSlots = $bookedSlots[$court->id] ?? [];
                                @endphp
                                
                                @if(empty($timeSlots))
                                    <div class="text-center py-8">
                                        <div class="text-gray-500">
                                            <i class="fas fa-calendar-times text-2xl mb-2"></i>
                                            <p>Jadwal belum tersedia</p>
                                            <p class="text-sm">Admin belum mengatur jadwal untuk hari ini</p>
                                        </div>
                                    </div>
                                @else
                                <div class="grid grid-cols-3 gap-2">
                                      @foreach($timeSlots as $slot)
                                    @php
                                        $isBooked = in_array($slot, $courtBookedSlots);
                                        // Extract hour from slot for comparison (slot is now just "07:00" format)
                                        $slotTime = $slot;
                                        $isPastTime = $selectedDate->isToday() &&
                                             Carbon\Carbon::now()->format('H:i') > $slotTime;
                                    @endphp
                                    <button class="time-slot p-2 rounded-lg text-xs font-medium transition-all duration-300
                                         {{ $isBooked ? 'bg-red-500 text-white cursor-not-allowed' :
                                            ($isPastTime ? 'bg-gray-300 text-gray-500 cursor-not-allowed' :
                                            'bg-green-500 hover:bg-green-600 text-white hover:scale-105') }}"
                                        {{ ($isBooked || $isPastTime) ? 'disabled' : '' }}
                                        data-court-id="{{ $court->id }}"
                                        data-court-number="{{ $court->court_number }}"
                                        data-sport-type="{{ $court->type }}"
                                        data-date="{{ $selectedDateStr }}"
                                        data-time="{{ $slot }}"                                        data-price="{{ $court->price_per_hour }}">
                                        {{ $slot }}
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>    <!-- Date Picker Modal -->
    <div id="datePickerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="backdrop-filter: blur(2px);">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl border">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Pilih Tanggal</h3>
                    <button id="closeDatePicker" class="text-gray-400 hover:text-gray-600 p-1 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Calendar Navigation -->
                <div class="flex items-center justify-between mb-4">
                    <button id="prevMonth" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </button>
                    <h4 id="currentMonth" class="text-lg font-semibold text-gray-900"></h4>
                    <button id="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-chevron-right text-gray-600"></i>
                    </button>
                </div>
                
                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1 mb-4">
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Min</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Sen</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Sel</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Rab</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Kam</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Jum</div>
                    <div class="text-center text-xs font-medium text-gray-500 py-2">Sab</div>
                </div>
                
                <div id="calendarDays" class="grid grid-cols-7 gap-1 mb-4" style="min-height: 200px;">
                    <!-- Calendar days will be generated by JavaScript -->
                </div>
                
                <div class="flex space-x-3">
                    <button id="selectToday" class="flex-1 bg-teal-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-teal-700 transition-colors">
                        Hari Ini
                    </button>
                    <button id="cancelDatePicker" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Confirmation Modal -->    <!-- Booking Modal - DISABLED (functionality removed) -->
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
    // Inject server-side data for JavaScript
    const bookedSlotsData = @json($bookedSlots);
    const availableTimeSlotsData = @json($availableTimeSlots);
    
    document.addEventListener('DOMContentLoaded', function() {
        // Date picker variables
        let currentDate = new Date();
        currentDate.setDate(1); // Set to first day of current month for navigation
        let selectedDateForCourt = new Date('{{ $selectedDate->format('Y-m-d') }}');
        selectedDateForCourt.setHours(0, 0, 0, 0); // Reset time
        let currentCourtId = null;
        const datePickerModal = document.getElementById('datePickerModal');
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        // Initialize calendar functionality
        initCalendarButtons();
        
        function initCalendarButtons() {
            const calendarBtns = document.querySelectorAll('.calendar-btn');
            calendarBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    currentCourtId = this.getAttribute('data-court-id');
                    const courtNumber = this.getAttribute('data-court-number');
                    
                    // Update modal title
                    document.querySelector('#datePickerModal h3').textContent = 
                        `Pilih Tanggal - Lapangan ${courtNumber}`;
                    
                    // Show date picker
                    showDatePicker();
                });
            });
        }        function showDatePicker() {
            // Set current date to the month of currently selected date for this court
            const courtContainer = document.querySelector(`[data-court-id="${currentCourtId}"]`);
            const courtDateDisplay = courtContainer.querySelector('.date-display');
            const currentDisplayedDate = courtDateDisplay.textContent;
            
            // Try to get the current date from the court's display, otherwise use selected date
            if (currentDisplayedDate && currentDisplayedDate !== 'Memuat...') {
                // Parse the displayed date to set calendar month
                const today = new Date();
                currentDate = new Date(today.getFullYear(), today.getMonth(), 1);
            } else {
                currentDate = new Date(selectedDateForCourt.getFullYear(), selectedDateForCourt.getMonth(), 1);
            }
            
            generateCalendar();
            datePickerModal.classList.remove('hidden');
        }        function generateCalendar() {
            console.log('Generating calendar for:', currentDate.getFullYear(), currentDate.getMonth() + 1);
            const currentMonthElement = document.getElementById('currentMonth');
            const calendarDaysElement = document.getElementById('calendarDays');
            
            currentMonthElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            
            // Clear previous calendar
            calendarDaysElement.innerHTML = '';
            
            // Get first day of month and number of days
            const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const firstDayWeekday = firstDayOfMonth.getDay(); // 0 = Sunday, 1 = Monday, etc.
            const daysInMonth = lastDayOfMonth.getDate();
            
            // Adjust for Monday start (Indonesian calendar)
            const startDay = firstDayWeekday === 0 ? 6 : firstDayWeekday - 1;
            
            // Add empty cells for days before month starts
            for (let i = 0; i < startDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day disabled';
                emptyDay.style.cursor = 'default';
                calendarDaysElement.appendChild(emptyDay);
            }
            
            // Add days of the month
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to compare dates only
            const minDate = new Date(today);
            const maxDate = new Date(today);
            maxDate.setDate(today.getDate() + 30);
            
            console.log('Date range:', minDate.toDateString(), 'to', maxDate.toDateString());
              for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                dayDate.setHours(0, 0, 0, 0); // Reset time to compare dates only
                
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                // Add explicit styling attributes
                dayElement.style.cursor = 'default';
                dayElement.style.userSelect = 'none';
                
                // Check if date is selectable
                if (dayDate < minDate) {
                    dayElement.classList.add('disabled');
                    dayElement.style.cursor = 'not-allowed';
                } else if (dayDate > maxDate) {
                    dayElement.classList.add('disabled');
                    dayElement.style.cursor = 'not-allowed';
                } else {
                    // Date is selectable - add clickable styling
                    dayElement.classList.add('clickable');
                    dayElement.style.cursor = 'pointer';
                    
                    // Check if it's today
                    if (dayDate.getTime() === today.getTime()) {
                        dayElement.classList.add('today');
                    }
                    
                    // Check if it's selected date
                    const selectedDateCopy = new Date(selectedDateForCourt);
                    selectedDateCopy.setHours(0, 0, 0, 0);
                    if (dayDate.getTime() === selectedDateCopy.getTime()) {
                        dayElement.classList.add('selected');
                    }
                      // Add click handler with proper date closure
                    dayElement.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        // Create date in local timezone without time conversion issues
                        const clickedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day, 12, 0, 0, 0);
                        console.log('Date clicked:', clickedDate.toDateString(), 'Day value:', day);
                        selectDate(clickedDate);
                    });
                    
                    // Add additional hover effects
                    dayElement.addEventListener('mouseenter', function() {
                        this.style.cursor = 'pointer';
                        if (!this.classList.contains('disabled')) {
                            this.style.transform = 'scale(1.1)';
                            this.style.backgroundColor = '#f3f4f6';
                        }
                    });
                    
                    dayElement.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('disabled')) {
                            this.style.transform = 'scale(1)';
                            if (!this.classList.contains('today') && !this.classList.contains('selected')) {
                                this.style.backgroundColor = 'transparent';
                            }
                        }
                    });
                }
                
                calendarDaysElement.appendChild(dayElement);
            }
        }        function selectDate(date) {
            console.log('selectDate called with:', date.toDateString(), 'Date object:', date);
            selectedDateForCourt = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0, 0);
            
            // Update visual selection in calendar before closing
            const calendarDays = document.querySelectorAll('.calendar-day');
            calendarDays.forEach(day => {
                day.classList.remove('selected');
                day.style.backgroundColor = '';
                day.style.color = '';
                
                // Re-apply today styling if needed
                if (day.classList.contains('today')) {
                    day.style.backgroundColor = '#ccfbf1';
                    day.style.color = '#0f766e';
                }
                
                // Apply selected styling to clicked date
                if (day.textContent == date.getDate() && !day.classList.contains('disabled')) {
                    day.classList.add('selected');
                    day.style.backgroundColor = '#0d9488';
                    day.style.color = 'white';
                    day.style.fontWeight = '500';
                }
            });
            
            console.log('Starting court schedule update for court:', currentCourtId);
            
            // Small delay to show selection before closing
            setTimeout(() => {
                updateCourtSchedule(currentCourtId, selectedDateForCourt);
                datePickerModal.classList.add('hidden');
            }, 300);
        }async function updateCourtSchedule(courtId, date) {
            try {
                // Show loading state
                const courtContainer = document.querySelector(`[data-court-id="${courtId}"]`);
                const dateDisplay = courtContainer.querySelector('.date-display');
                const timeSlotsContainer = courtContainer.querySelector('.time-slots-container');
                
                dateDisplay.textContent = 'Memuat...';
                timeSlotsContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i></div>';
                
                // Format date for API call - using local date formatting to avoid timezone issues
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;
                
                console.log('Formatted date string:', dateString, 'from date:', date.toDateString());
                
                // Fetch new schedule data (simulate API call for now)
                const scheduleData = await fetchCourtSchedule(courtId, dateString);
                
                // Update date display
                dateDisplay.textContent = formatDateInIndonesian(date);
                
                // Update time slots
                generateTimeSlots(timeSlotsContainer, courtId, dateString, scheduleData);
                
            } catch (error) {
                console.error('Error updating schedule:', error);
                
                // Format date string for fallback
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;
                
                // Show error state instead of infinite loading
                const courtContainer = document.querySelector(`[data-court-id="${courtId}"]`);
                const dateDisplay = courtContainer.querySelector('.date-display');
                const timeSlotsContainer = courtContainer.querySelector('.time-slots-container');
                
                dateDisplay.textContent = formatDateInIndonesian(date);
                timeSlotsContainer.innerHTML = '<div class="text-center py-4 text-red-500"><i class="fas fa-exclamation-triangle"></i><br>Gagal memuat jadwal</div>';
                
                // Fallback to page reload as last resort
                setTimeout(() => {
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('date', dateString);
                    window.location.href = currentUrl.toString();
                }, 2000);
            }
        }        async function fetchCourtSchedule(courtId, dateString) {
            try {
                // Make real API call to Laravel backend
                const response = await fetch(`/user/field-detail/ajax/${courtId}?date=${dateString}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                // Update global availableTimeSlotsData with fresh data from admin
                if (data.availableTimeSlots) {
                    availableTimeSlotsData[courtId] = data.availableTimeSlots;
                }
                
                return { 
                    bookedSlots: data.bookedSlots || [],
                    availableTimeSlots: data.availableTimeSlots || []
                };
                
            } catch (error) {
                console.error('Error fetching schedule:', error);
                // Return empty data on error - let generateTimeSlots handle empty schedules
                return { 
                    bookedSlots: [],
                    availableTimeSlots: []
                };
            }
        }        function generateTimeSlots(container, courtId, dateString, scheduleData) {
            console.log('Generating time slots for court:', courtId, 'date:', dateString);
            
            // Use fresh data from AJAX if available, otherwise fall back to initial data
            let timeSlots;
            if (scheduleData.availableTimeSlots && scheduleData.availableTimeSlots.length > 0) {
                // Use fresh data from AJAX
                timeSlots = scheduleData.availableTimeSlots;
                // Update global data for consistency
                availableTimeSlotsData[courtId] = timeSlots;
            } else {
                // Fall back to initial data from page load
                timeSlots = availableTimeSlotsData[courtId] || [];
            }
            
            console.log('Using time slots:', timeSlots);
            
            // If no admin schedules, show message
            if (timeSlots.length === 0) {
                container.innerHTML = '<div class="text-center py-8"><div class="text-gray-500"><i class="fas fa-calendar-times text-2xl mb-2"></i><p>Jadwal belum tersedia</p><p class="text-sm">Admin belum mengatur jadwal untuk hari ini</p></div></div>';
                return;
            }
            
            const selectedDate = new Date(dateString);
            const today = new Date();
            const bookedSlots = scheduleData.bookedSlots || bookedSlotsData[courtId] || [];
            
            console.log('Booked slots:', bookedSlots);
            
            let slotsHTML = '<div class="grid grid-cols-3 gap-2">';
            
            timeSlots.forEach(slot => {
                const isBooked = bookedSlots.includes(slot);
                // For single time format (e.g., "07:00"), parse directly
                const slotHour = parseInt(slot.split(':')[0]);
                const isPastTime = selectedDate.toDateString() === today.toDateString() &&
                                 today.getHours() > slotHour;
                
                let buttonClass = 'time-slot p-2 rounded-lg text-xs font-medium transition-all duration-300 ';
                
                if (isBooked) {
                    buttonClass += 'bg-red-500 text-white cursor-not-allowed';
                } else if (isPastTime) {
                    buttonClass += 'bg-gray-300 text-gray-500 cursor-not-allowed';
                } else {
                    buttonClass += 'bg-green-500 hover:bg-green-600 text-white hover:scale-105';
                }
                
                const courtElement = document.querySelector(`[data-court-id="${courtId}"]`);
                if (!courtElement) {
                    console.error('Court element not found for ID:', courtId);
                    return;
                }
                
                const courtNumber = courtElement.querySelector('h3').textContent.replace('Lapangan ', '');
                const sportTabElement = document.querySelector('.sport-tab.bg-teal-600');
                const sportType = sportTabElement ? sportTabElement.textContent.toLowerCase() : 'badminton';
                
                slotsHTML += `
                    <button class="${buttonClass}"
                        ${(isBooked || isPastTime) ? 'disabled' : ''}
                        data-court-id="${courtId}"
                        data-court-number="${courtNumber}"
                        data-sport-type="${sportType}"
                        data-date="${dateString}"
                        data-time="${slot}"
                        data-price="100000">
                        ${slot}
                    </button>
                `;
            });
            
            slotsHTML += '</div>';
            container.innerHTML = slotsHTML;
            
            console.log('Time slots generated successfully, reinitializing handlers...');
            
            // Re-initialize time slot event handlers
            initTimeSlotHandlers();
            
            console.log('Time slot generation complete for court:', courtId);
        }

        function formatDateInIndonesian(date) {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[date.getDay()];
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            
            return `${dayName}, ${day} ${month} ${year}`;
        }        // Calendar navigation
        document.getElementById('prevMonth').addEventListener('click', function() {
            currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
            generateCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
            generateCalendar();
        });

        // Date picker modal controls
        document.getElementById('closeDatePicker').addEventListener('click', function() {
            datePickerModal.classList.add('hidden');
        });

        document.getElementById('cancelDatePicker').addEventListener('click', function() {
            datePickerModal.classList.add('hidden');
        });        document.getElementById('selectToday').addEventListener('click', function() {
            const today = new Date();
            const todayAtNoon = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12, 0, 0, 0);
            selectDate(todayAtNoon);
        });

        // Close modal when clicking outside
        datePickerModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Tab functionality for mixed venues
        const sportTabs = document.querySelectorAll('.sport-tab');
        const sportContents = document.querySelectorAll('.sport-content');
        
        sportTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetSport = this.getAttribute('data-sport');
                
                // Update tab active state
                sportTabs.forEach(t => {
                    t.classList.remove('bg-teal-600', 'text-white');
                    t.classList.add('bg-gray-300', 'text-gray-700');
                });
                this.classList.remove('bg-gray-300', 'text-gray-700');
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
        });

        // Initialize time slot handlers
        initTimeSlotHandlers();        function initTimeSlotHandlers() {
            const timeSlots = document.querySelectorAll('.time-slot');
            // const bookingModal = document.getElementById('bookingModal'); // Disabled
            
            timeSlots.forEach(slot => {
                // Remove existing event listeners
                slot.replaceWith(slot.cloneNode(true));
            });
            
            // Re-query after replacing nodes
            const newTimeSlots = document.querySelectorAll('.time-slot');            newTimeSlots.forEach(slot => {
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

                // Hover effects for available slots
                slot.addEventListener('mouseenter', function() {
                    if (!this.disabled && !this.classList.contains('bg-blue-600') && !this.classList.contains('bg-red-500') && !this.classList.contains('bg-gray-300')) {
                        this.classList.add('transform', 'scale-105');
                    }
                });

                slot.addEventListener('mouseleave', function() {
                    if (!this.disabled && !this.classList.contains('bg-blue-600') && !this.classList.contains('bg-red-500') && !this.classList.contains('bg-gray-300')) {
                        this.classList.remove('transform', 'scale-105');
                    }
                });
            });
        }        // Booking modal functionality disabled
        // const bookingModal = document.getElementById('bookingModal');
        // const confirmBookingBtn = document.getElementById('confirmBooking');
        // const cancelBookingBtn = document.getElementById('cancelBooking');

        // Modal event listeners commented out - booking functionality disabled
        /*
        cancelBookingBtn.addEventListener('click', function() {
            bookingModal.classList.add('hidden');
            // Reset slot selection
            const timeSlots = document.querySelectorAll('.time-slot');
            timeSlots.forEach(s => {
                s.classList.remove('bg-blue-600', 'ring-4', 'ring-blue-300');
                if (!s.disabled && !s.classList.contains('bg-red-500') && !s.classList.contains('bg-gray-300')) {
                    s.classList.add('bg-green-500');
                }
            });
        });

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
            }
        });
        */

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function formatPrice(price) {
            return parseInt(price).toLocaleString('id-ID');
        }
    });
    </script>
</body>
</html>