<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::create([
            'id' => Str::uuid(),
            'name' => 'Traki',
        ]);

        $this->command->info('✅ Store creada: ' . $store->name . ' (ID: ' . $store->id . ')');
    }
}
