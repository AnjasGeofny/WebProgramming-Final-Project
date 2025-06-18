<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Tipe olahraga yang ada di database:\n";
$types = App\Models\Field::distinct()->pluck('type');

foreach ($types as $type) {
    $count = App\Models\Field::where('type', $type)->count();
    echo "- {$type}: {$count} lapangan\n";
}

echo "\nTotal lapangan: " . App\Models\Field::count() . "\n";
