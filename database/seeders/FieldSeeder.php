<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Field;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing fields with foreign key consideration
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Field::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $fields = [
            // Futsal fields (2 courts each)
            [
                'name' => 'Balikpapan Sport Center',
                'type' => 'Futsal',
                'location' => 'Jl. Jenderal Sudirman No.1, Balikpapan',
                'price_per_hour' => 75000,
            ],
            [
                'name' => 'Family Sport Clinic',
                'type' => 'Futsal',
                'location' => 'Jl. Soekarno Hatta No.25, Balikpapan',
                'price_per_hour' => 75000,
            ],
            // Badminton fields (2 courts each)
            [
                'name' => 'Global Sport',
                'type' => 'Badminton',
                'location' => 'Jl. MT Haryono No.17, Balikpapan',
                'price_per_hour' => 75000,
            ],
            [
                'name' => 'Sepinggan Pratama',
                'type' => 'Badminton',
                'location' => 'Jl. Marsma R. Iswahyudi No.10, Balikpapan',
                'price_per_hour' => 85000,
            ],
            // Badminton & Futsal fields (4 courts each: 2 futsal + 2 badminton)
            [
                'name' => 'Balikpapan Multiplex Arena',
                'type' => 'Badminton & Futsal',
                'location' => 'Jl. Ahmad Yani No.5, Balikpapan',
                'price_per_hour' => 90000,
            ],
            [
                'name' => 'Champion Sport Complex',
                'type' => 'Badminton & Futsal',
                'location' => 'Jl. Pupuk Raya No.12, Balikpapan',
                'price_per_hour' => 95000,
            ],
        ];

        foreach ($fields as $fieldData) {
            if ($fieldData['type'] === 'Badminton & Futsal') {
                // For Badminton & Futsal: create 2 futsal courts + 2 badminton courts

                // Create 2 Futsal courts
                for ($i = 1; $i <= 2; $i++) {
                    Field::create([
                        'name' => $fieldData['name'],
                        'type' => 'Futsal',
                        'location' => $fieldData['location'],
                        'price_per_hour' => $fieldData['price_per_hour'],
                        'court_number' => $i,
                    ]);
                }

                // Create 2 Badminton courts
                for ($i = 1; $i <= 2; $i++) {
                    Field::create([
                        'name' => $fieldData['name'],
                        'type' => 'Badminton',
                        'location' => $fieldData['location'],
                        'price_per_hour' => $fieldData['price_per_hour'],
                        'court_number' => $i,
                    ]);
                }
            } else {
                // For single sport type: create 2 courts
                for ($i = 1; $i <= 2; $i++) {
                    Field::create([
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'location' => $fieldData['location'],
                        'price_per_hour' => $fieldData['price_per_hour'],
                        'court_number' => $i,
                    ]);
                }
            }
        }
    }
}
