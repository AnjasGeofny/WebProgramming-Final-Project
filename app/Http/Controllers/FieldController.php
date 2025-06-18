<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FieldController extends Controller
{
    // Halaman Top Fields (halaman utama fields)
    public function index()
    {
        // Ambil venue unik berdasarkan nama untuk ditampilkan di Top Fields
        $topFields = Field::all()
            ->groupBy('name')
            ->map(function ($group) {
                // Get the first field for each venue name
                return $group->first();
            })
            ->take(4)
            ->values();

        return view('user.fields', compact('topFields'));
    }

    // Halaman All Fields (daftar lengkap)
    public function allFields(Request $request)
    {
        $query = Field::query();

        // Filter berdasarkan type jika ada parameter
        if ($request->has('type') && $request->type !== 'all') {
            if ($request->type === 'badminton-futsal') {
                // Untuk filter "Badminton & Futsal", hanya tampilkan venue yang memiliki KEDUA jenis
                $venuesWithBoth = Field::select('name')
                    ->whereIn('type', ['Futsal', 'Badminton'])
                    ->groupBy('name')
                    ->havingRaw('COUNT(DISTINCT type) = 2')
                    ->pluck('name');

                $query->whereIn('name', $venuesWithBoth);
            } else {
                $query->where('type', ucfirst($request->type));
            }
        }

        // Group fields by venue name and get unique venues with their sports
        $fieldsGrouped = $query->get()
            ->groupBy('name')
            ->map(function ($group) {
                $venue = $group->first();
                $sportsTypes = $group->pluck('type')->unique()->sort()->values();

                // Get one representative field for each venue
                $venue->available_sports = $sportsTypes;
                $venue->total_courts = $group->count();

                // For display purposes, create a combined type
                if ($sportsTypes->count() > 1) {
                    $venue->display_type = $sportsTypes->join(' & ');
                } else {
                    $venue->display_type = $sportsTypes->first();
                }

                return $venue;
            })
            ->values();

        return view('user.all-fields', compact('fieldsGrouped'));
    }

    public function show($id, Request $request)
    {
        $field = Field::findOrFail($id);

        // Get all courts for this venue (same name, all types and court numbers)
        $allCourts = Field::where('name', $field->name)
            ->orderBy('type')
            ->orderBy('court_number')
            ->get();

        // Group courts by sport type
        $courtsByType = $allCourts->groupBy('type');

        // Get selected date from request or default to today
        $selectedDate = $request->input('date') ?
            Carbon::createFromFormat('Y-m-d', $request->input('date')) :
            Carbon::now();

        // Ensure selected date is not in the past
        if ($selectedDate->isPast() && !$selectedDate->isToday()) {
            $selectedDate = Carbon::now();
        }

        // Get court IDs
        $courtIds = $allCourts->pluck('id');

        // Get schedules from admin for the selected date
        // Show ALL schedules created by admin, regardless of availability status
        $schedules = Schedule::whereIn('field_id', $courtIds)
            ->where('date', $selectedDate->format('Y-m-d'))
            ->orderBy('start_time')  // Add explicit ordering
            ->get();

        // Convert schedules to time slots format that matches the view
        $bookedSlots = [];
        $availableTimeSlots = [];

        foreach ($schedules as $schedule) {
            $courtId = $schedule->field_id;
            // Use only start time for display (e.g., "07:00" instead of "07:00 - 08:00")
            $timeSlot = substr($schedule->start_time, 0, 5);

            // Add ALL schedules to available time slots for display
            if (!isset($availableTimeSlots[$courtId])) {
                $availableTimeSlots[$courtId] = [];
            }
            $availableTimeSlots[$courtId][] = $timeSlot;

            // Check if this schedule has bookings (including multiple schedule bookings)
            $hasBookings = $schedule->getAllBookings()->count() > 0;
            if ($hasBookings) {
                if (!isset($bookedSlots[$courtId])) {
                    $bookedSlots[$courtId] = [];
                }
                $bookedSlots[$courtId][] = $timeSlot;
            }
        }

        // Sort the available time slots for each court to ensure proper display order
        foreach ($availableTimeSlots as $courtId => $slots) {
            sort($availableTimeSlots[$courtId]);
        }

        // If no schedules from admin, fall back to existing booking system
        if ($schedules->isEmpty()) {
            $existingBookings = Booking::with('schedule', 'field')
                ->whereIn('field_id', $courtIds)
                ->whereHas('schedule', function ($query) use ($selectedDate) {
                    $query->where('date', $selectedDate->format('Y-m-d'));
                })
                ->get();

            // Convert existing bookings to bookedSlots format
            foreach ($existingBookings as $booking) {
                $courtId = $booking->field_id;
                // Use only start time for display consistency
                $timeSlot = substr($booking->schedule->start_time, 0, 5);

                if (!isset($bookedSlots[$courtId])) {
                    $bookedSlots[$courtId] = [];
                }
                $bookedSlots[$courtId][] = $timeSlot;
            }
        }

        return view('user.field-detail', compact('field', 'allCourts', 'courtsByType', 'bookedSlots', 'availableTimeSlots', 'selectedDate'));
    }

    /**
     * Get court schedule data via AJAX for dynamic date selection
     */
    public function getCourtScheduleAjax($courtId, Request $request)
    {
        try {
            $date = $request->input('date', Carbon::now()->format('Y-m-d'));
            $selectedDate = Carbon::createFromFormat('Y-m-d', $date);

            // Validate field exists
            $field = Field::findOrFail($courtId);

            // Get schedules for this specific field/court and date
            // Show ALL schedules created by admin, regardless of availability status
            $schedules = Schedule::where('field_id', $courtId)
                ->where('date', $selectedDate->format('Y-m-d'))
                ->orderBy('start_time')  // Add explicit ordering
                ->get();

            $bookedSlots = [];
            $availableTimeSlots = [];

            foreach ($schedules as $schedule) {
                // Use only start time for display (e.g., "07:00" instead of "07:00 - 08:00")
                $timeSlot = substr($schedule->start_time, 0, 5);

                // Add ALL schedules to available time slots for display
                $availableTimeSlots[] = $timeSlot;

                // Check if this schedule has bookings (including multiple schedule bookings)
                $hasBookings = $schedule->getAllBookings()->count() > 0;
                if ($hasBookings) {
                    $bookedSlots[] = $timeSlot;
                }
            }

            // Sort the time slots to ensure proper display order
            sort($availableTimeSlots);
            sort($bookedSlots);

            return response()->json([
                'success' => true,
                'bookedSlots' => $bookedSlots,
                'availableTimeSlots' => $availableTimeSlots,
                'date' => $date
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching schedule data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error fetching schedule data',
                'bookedSlots' => [],
                'availableTimeSlots' => []
            ], 500);
        }
    }
}
