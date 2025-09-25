<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la store principal
        $store = \App\Models\Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear usuarios');
            return;
        }

        // Create default admin user
        $this->createAdminUser($store);

        // Create sample customer users
        $this->createSampleCustomers();

        // Create sample moderator
        $this->createModeratorUser($store);
    }

    /**
     * Create default admin user
     */
    private function createAdminUser($store): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador del Sistema',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'phone' => '+58 412 1234567',
                'email_verified_at' => now(),
                'store_id' => $store->id,
            ]
        );

        $this->command->info('✅ Usuario administrador creado: admin@example.com / admin123');
    }

    /**
     * Create sample customer users
     */
    private function createSampleCustomers(): void
    {
        $customers = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'phone' => '+58 424 9876543',
            ],
            [
                'name' => 'María González',
                'email' => 'maria@example.com',
                'phone' => '+58 414 5551234',
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos@example.com',
                'phone' => '+58 426 7778888',
            ],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_CUSTOMER,
                    'status' => User::STATUS_ACTIVE,
                    'phone' => $customer['phone'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Usuarios clientes de prueba creados');
    }

    /**
     * Create sample moderator user
     */
    private function createModeratorUser($store): void
    {
        User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderador de Contenido',
                'email' => 'moderator@example.com',
                'password' => Hash::make('moderator123'),
                'role' => User::ROLE_MODERATOR,
                'status' => User::STATUS_ACTIVE,
                'phone' => '+58 416 5554321',
                'email_verified_at' => now(),
                'store_id' => $store->id,
            ]
        );

        $this->command->info('✅ Usuario moderador creado: moderator@example.com / moderator123');
    }
}
