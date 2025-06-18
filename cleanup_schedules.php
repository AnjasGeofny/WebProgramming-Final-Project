<?php

use App\Models\Schedule;

// Hapus schedule 12:00-13:00 dan 18:00-19:00 yang tidak memiliki booking
$schedules1200 = Schedule::where('start_time', '12:00:00')
    ->where('end_time', '13:00:00')
    ->whereDoesntHave('bookings')
    ->get();

$schedules1800 = Schedule::where('start_time', '18:00:00')
    ->where('end_time', '19:00:00')
    ->whereDoesntHave('bookings')
    ->get();

$deleted1200 = 0;
$deleted1800 = 0;

foreach ($schedules1200 as $schedule) {
    $schedule->delete();
    $deleted1200++;
}

foreach ($schedules1800 as $schedule) {
    $schedule->delete();
    $deleted1800++;
}

echo "Berhasil menghapus:\n";
echo "- {$deleted1200} schedule jam 12:00-13:00\n";
echo "- {$deleted1800} schedule jam 18:00-19:00\n";
echo "Total: " . ($deleted1200 + $deleted1800) . " schedule dihapus\n";
