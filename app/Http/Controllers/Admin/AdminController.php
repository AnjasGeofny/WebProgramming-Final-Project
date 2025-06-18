<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Payment;
use App\Services\AdminIntegrationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminIntegrationService $adminService)
    {
        $this->adminService = $adminService;
    }
    public function dashboard()
    {
        // Stats untuk dashboard
        $stats = [
            'total_fields' => Field::count(),
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('created_at', Carbon::today())->count(),
            'total_users' => User::count(),
            'total_revenue' => Booking::join('payments', 'bookings.id', '=', 'payments.booking_id')
                ->where('payments.payment_status', 'paid')
                ->sum('bookings.total_price'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        // Recent bookings
        $recentBookings = Booking::with(['user', 'field', 'schedule'])
            ->latest()
            ->take(10)
            ->get();

        // Field utilization
        $fieldUtilization = Field::withCount([
            'bookings' => function ($query) {
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
            }
        ])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'fieldUtilization'));
    }

    public function fields()
    {
        $fields = Field::with([
            'bookings' => function ($query) {
                $query->whereDate('created_at', '>=', Carbon::now()->subDays(30));
            }
        ])
            ->withCount('bookings')
            ->paginate(20);

        return view('admin.fields.index', compact('fields'));
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'field', 'schedule', 'payments']);

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter berdasarkan field
        if ($request->has('field_id') && $request->field_id !== 'all') {
            $query->where('field_id', $request->field_id);
        }

        $bookings = $query->latest()->paginate(20);
        $fields = Field::all();

        return view('admin.bookings.index', compact('bookings', 'fields'));
    }

    public function schedules(Request $request)
    {
        $query = Schedule::with(['field', 'bookings']);

        // Filter berdasarkan field
        if ($request->has('field_id') && $request->field_id !== 'all') {
            $query->where('field_id', $request->field_id);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        } else {
            // Default ke hari ini jika tidak ada filter tanggal
            $query->whereDate('date', Carbon::today());
        }

        // Filter berdasarkan availability
        if ($request->has('availability') && $request->availability !== 'all') {
            $available = $request->availability === 'available';
            $query->where('is_available', $available);
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->paginate(20);
        $fields = Field::all();

        return view('admin.schedules.index', compact('schedules', 'fields'));
    }

    public function analytics()
    {
        // Data untuk analytics
        $bookingsByMonth = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month');

        $revenueByMonth = Booking::join('payments', 'bookings.id', '=', 'payments.booking_id')
            ->where('payments.payment_status', 'paid')
            ->selectRaw('MONTH(bookings.created_at) as month, SUM(bookings.total_price) as revenue')
            ->whereYear('bookings.created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('revenue', 'month');

        $fieldTypeDistribution = Field::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $bookingsByFieldType = Booking::join('fields', 'bookings.field_id', '=', 'fields.id')
            ->selectRaw('fields.type, COUNT(bookings.id) as count')
            ->groupBy('fields.type')
            ->pluck('count', 'type');

        return view('admin.analytics', compact(
            'bookingsByMonth',
            'revenueByMonth',
            'fieldTypeDistribution',
            'bookingsByFieldType'
        ));
    }

    public function users()
    {
        $users = User::withCount(['bookings'])
            ->withSum([
                'bookings' => function ($query) {
                    $query->join('payments', 'bookings.id', '=', 'payments.booking_id')
                        ->where('payments.payment_status', 'paid');
                }
            ], 'total_price')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function getBookingData(Request $request)
    {
        $fieldId = $request->field_id;
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $schedules = Schedule::where('field_id', $fieldId)
            ->whereDate('date', $date)
            ->with('bookings')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'schedules' => $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'is_available' => $schedule->is_available,
                    'is_booked' => $schedule->bookings->count() > 0,
                    'booking_info' => $schedule->bookings->map(function ($booking) {
                        return [
                            'id' => $booking->id,
                            'user_name' => $booking->user->name,
                            'status' => $booking->status,
                            'total_price' => $booking->total_price,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * API Methods untuk Widget Real-time
     */
    public function getFieldStatus()
    {
        return response()->json($this->adminService->getFieldStatus());
    }

    public function getLiveBookings()
    {
        return response()->json($this->adminService->getLiveBookings());
    }

    public function getDashboardStats()
    {
        return response()->json($this->adminService->getDashboardStats());
    }

    public function updateScheduleAvailability(Request $request, $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->update(['is_available' => $request->is_available]);

        return response()->json(['success' => true]);
    }
}
