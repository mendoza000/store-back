<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users with default admin and sample data
        $this->call(UserSeeder::class);

        // Uncomment to create additional random users for testing
        // User::factory(10)->create();
    }
}
