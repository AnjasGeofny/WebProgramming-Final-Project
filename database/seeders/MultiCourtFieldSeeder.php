<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Field;

class MultiCourtFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing fields with same names to avoid duplicates
        Field::whereIn('name', [
            'Balikpapan Multiplex Arena',
            'Champion Sport Complex',
            'Elite Sports Hub'
        ])->delete();

        // Create Balikpapan Multiplex Arena with 2 courts
        Field::create([
            'name' => 'Balikpapan Multiplex Arena',
            'location' => 'Jl. Ahmad Yani No.5, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 1,
            'price_per_hour' => 90000,
        ]);

        Field::create([
            'name' => 'Balikpapan Multiplex Arena',
            'location' => 'Jl. Ahmad Yani No.5, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 2,
            'price_per_hour' => 90000,
        ]);

        // Create Champion Sport Complex with 2 courts
        Field::create([
            'name' => 'Champion Sport Complex',
            'location' => 'Jl. Pupuk Raya No.12, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 1,
            'price_per_hour' => 95000,
        ]);

        Field::create([
            'name' => 'Champion Sport Complex',
            'location' => 'Jl. Pupuk Raya No.12, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 2,
            'price_per_hour' => 95000,
        ]);

        // Create Elite Sports Hub with 3 courts
        Field::create([
            'name' => 'Elite Sports Hub',
            'location' => 'Jl. Sepinggan No.8, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 1,
            'price_per_hour' => 100000,
        ]);

        Field::create([
            'name' => 'Elite Sports Hub',
            'location' => 'Jl. Sepinggan No.8, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 2,
            'price_per_hour' => 100000,
        ]);

        Field::create([
            'name' => 'Elite Sports Hub',
            'location' => 'Jl. Sepinggan No.8, Balikpapan',
            'type' => 'Badminton & Futsal',
            'court_number' => 3,
            'price_per_hour' => 100000,
        ]);
    }
}
