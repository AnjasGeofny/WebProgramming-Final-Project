<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Field;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class TestBookingFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-flow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete booking flow functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏸 Testing Booking Flow...');

        // Test 1: Check if we have data
        $this->info('📊 Checking data availability...');

        $fieldsCount = Field::count();
        $schedulesCount = Schedule::count();
        $usersCount = User::count();

        $this->table(['Model', 'Count'], [
            ['Fields', $fieldsCount],
            ['Schedules', $schedulesCount],
            ['Users', $usersCount],
        ]);

        if ($fieldsCount == 0) {
            $this->warn('⚠️  No fields found. Please run seeders first: php artisan db:seed');
            return;
        }

        if ($schedulesCount == 0) {
            $this->warn('⚠️  No schedules found. Please run schedule seeder.');
            return;
        }

        // Test 2: Check field-schedule relationships
        $this->info('🔗 Testing field-schedule relationships...');

        $field = Field::first();
        $fieldSchedules = $field->schedules()->count();

        $this->info("Field '{$field->name}' has {$fieldSchedules} schedules");

        // Test 3: Check available schedules for today
        $this->info('📅 Checking available schedules for today...');

        $today = Carbon::today();
        $availableToday = Schedule::where('date', $today)
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->count();

        $bookedToday = Schedule::where('date', $today)
            ->whereHas('bookings', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->count();

        $this->table(['Status', 'Count'], [
            ['Available Today', $availableToday],
            ['Booked Today', $bookedToday],
        ]);

        // Test 4: Test booking creation
        $this->info('📝 Testing booking creation...');

        $user = User::first();
        if (!$user) {
            $this->warn('⚠️  No users found. Creating test user...');
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $availableSchedule = Schedule::where('date', '>=', today())
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->first();

        if (!$availableSchedule) {
            $this->warn('⚠️  No available schedules found for testing.');
            return;
        }
        try {
            $booking = Booking::create([
                'user_id' => $user->id,
                'field_id' => $availableSchedule->field_id,
                'schedule_id' => $availableSchedule->id,
                'user_name' => $user->name,
                'user_phone' => '081234567890',
                'status' => 'pending',
                'total_price' => $availableSchedule->field->price_per_hour,
            ]);

            $this->info("✅ Successfully created booking ID: {$booking->id}");
            $this->info("   - User: {$booking->user->name} ({$booking->user_name})");
            $this->info("   - Field: {$booking->field->name}");
            $this->info("   - Schedule: {$booking->schedule->date} {$booking->schedule->start_time}-{$booking->schedule->end_time}");
            $this->info("   - Price: Rp " . number_format($booking->total_price));

        } catch (\Exception $e) {
            $this->error("❌ Failed to create booking: " . $e->getMessage());
        }

        // Test 5: Check dashboard data
        $this->info('📈 Testing dashboard data...');

        $todayBookings = Booking::whereHas('schedule', function ($query) {
            $query->whereDate('date', Carbon::today());
        })->count();

        $totalRevenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');
        $pendingBookings = Booking::where('status', 'pending')->count();

        $this->table(['Metric', 'Value'], [
            ['Today Bookings', $todayBookings],
            ['Total Revenue', 'Rp ' . number_format($totalRevenue)],
            ['Pending Bookings', $pendingBookings],
        ]);

        // Test 6: Test schedule availability logic
        $this->info('🔍 Testing schedule availability logic...');

        $testSchedule = Schedule::first();
        $isAvailable = $testSchedule->isAvailable();

        $this->info("Schedule ID {$testSchedule->id} availability: " . ($isAvailable ? '✅ Available' : '❌ Not Available'));

        $this->info('🎉 Booking flow test completed!');

        return 0;
    }
}