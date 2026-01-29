<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmeInformation;

class SmeInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 SmeInformation records
        SmeInformation::factory()->count(10)->create();
    }
}
