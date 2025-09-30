<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Crear Store primero
            StoreSeeder::class,
            
            // 2. Crear usuarios
            UserSeeder::class,
            
            // 3. Crear categorías y productos
            CategorySeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            ProductVariantSeeder::class,
            
            // 4. Crear métodos de pago
            PaymentMethodSeeder::class,
            
            // 5. Crear carritos y órdenes
            CartSeeder::class,
            OrderSeeder::class,

        ]);
    }
}
