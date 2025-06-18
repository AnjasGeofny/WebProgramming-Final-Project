<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [];

        // Generate schedules for next 7 days
        for ($day = 0; $day < 7; $day++) {
            $date = Carbon::now()->addDays($day);

            // Time slots for each day
            $timeSlots = [
                ['07:00', '08:00'],
                ['08:00', '09:00'],
                ['09:00', '10:00'],
                ['10:00', '11:00'],
                ['11:00', '12:00'],
                ['13:00', '14:00'],
                ['14:00', '15:00'],
                ['15:00', '16:00'],
                ['16:00', '17:00'],
                ['17:00', '18:00'],
                ['18:00', '19:00'],
                ['19:00', '20:00'],
                ['20:00', '21:00'],
                ['21:00', '22:00'],
            ];

            foreach ($timeSlots as $slot) {
                $schedules[] = [
                    'date' => $date->format('Y-m-d'),
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Schedule::insert($schedules);
    }
}
