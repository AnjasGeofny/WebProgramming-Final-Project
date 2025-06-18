<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Field;
use App\Models\Schedule;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users (skip admin)
        $users = User::where('email', '!=', 'admin@sportcenter.com')->get();
        $fields = Field::all();
        $schedules = Schedule::whereDate('date', '>=', today())->limit(20)->get();

        if ($users->count() > 0 && $fields->count() > 0 && $schedules->count() > 0) {
            // Create some sample bookings
            $bookings = [
                [
                    'user_id' => $users->random()->id,
                    'field_id' => $fields->where('type', 'Futsal')->first()->id,
                    'schedule_id' => $schedules->random()->id,
                    'status' => 'confirmed',
                    'total_price' => 75000,
                ],
                [
                    'user_id' => $users->random()->id,
                    'field_id' => $fields->where('type', 'Badminton')->first()->id,
                    'schedule_id' => $schedules->random()->id,
                    'status' => 'pending',
                    'total_price' => 75000,
                ],
                [
                    'user_id' => $users->random()->id,
                    'field_id' => $fields->where('type', 'Futsal')->first()->id,
                    'schedule_id' => $schedules->random()->id,
                    'status' => 'confirmed',
                    'total_price' => 75000,
                ],
                [
                    'user_id' => $users->random()->id,
                    'field_id' => $fields->where('type', 'Badminton')->last()->id,
                    'schedule_id' => $schedules->random()->id,
                    'status' => 'completed',
                    'total_price' => 85000,
                ],
            ];

            foreach ($bookings as $booking) {
                Booking::create($booking);
            }
        }
    }
}
