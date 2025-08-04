<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️  No hay usuarios o productos disponibles para crear carritos');
            return;
        }

        $cartsCreated = 0;
        $itemsCreated = 0;

        // Crear carritos para usuarios registrados
        foreach ($users as $user) {
            // 70% de probabilidad de que el usuario tenga un carrito activo
            if (rand(1, 100) <= 70) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'expires_at' => now()->addDays(30),
                ]);

                $cartsCreated++;

                // Agregar 1-4 productos al carrito
                $numItems = rand(1, 4);
                $selectedProducts = $products->random($numItems);

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);

                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $product->price,
                    ]);

                    $itemsCreated++;
                }
            }
        }

        // Crear algunos carritos de invitados (sin user_id)
        $guestCarts = rand(3, 8);
        for ($i = 0; $i < $guestCarts; $i++) {
            $cart = Cart::create([
                'session_id' => \Illuminate\Support\Str::uuid(),
                'status' => 'active',
                'expires_at' => now()->addDays(30),
            ]);

            $cartsCreated++;

            // Agregar 1-3 productos al carrito de invitado
            $numItems = rand(1, 3);
            $selectedProducts = $products->random($numItems);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $itemsCreated++;
            }
        }

        // Crear algunos carritos expirados para testing
        $expiredCarts = rand(2, 5);
        for ($i = 0; $i < $expiredCarts; $i++) {
            $cart = Cart::create([
                'user_id' => $users->random()->id,
                'status' => 'expired',
                'expires_at' => now()->subDays(rand(1, 10)),
            ]);

            $cartsCreated++;

            // Agregar productos al carrito expirado
            $numItems = rand(1, 3);
            $selectedProducts = $products->random($numItems);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2);

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $itemsCreated++;
            }
        }

        // Crear algunos carritos completados (convertidos en pedidos)
        $completedCarts = rand(1, 3);
        for ($i = 0; $i < $completedCarts; $i++) {
            $cart = Cart::create([
                'user_id' => $users->random()->id,
                'status' => 'completed',
                'expires_at' => now()->subDays(rand(1, 5)),
            ]);

            $cartsCreated++;

            // Agregar productos al carrito completado
            $numItems = rand(1, 4);
            $selectedProducts = $products->random($numItems);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $itemsCreated++;
            }
        }

        $this->command->info("✅ Carritos creados exitosamente:");
        $this->command->info("   - {$cartsCreated} carritos creados");
        $this->command->info("   - {$itemsCreated} items agregados a carritos");

        // Mostrar estadísticas
        $activeCarts = Cart::where('status', 'active')->count();
        $expiredCarts = Cart::where('status', 'expired')->count();
        $completedCarts = Cart::where('status', 'completed')->count();
        $guestCarts = Cart::whereNull('user_id')->count();
        $registeredCarts = Cart::whereNotNull('user_id')->count();

        $this->command->info("📊 Estadísticas de carritos:");
        $this->command->info("   - Carritos activos: {$activeCarts}");
        $this->command->info("   - Carritos expirados: {$expiredCarts}");
        $this->command->info("   - Carritos completados: {$completedCarts}");
        $this->command->info("   - Carritos de invitados: {$guestCarts}");
        $this->command->info("   - Carritos de usuarios registrados: {$registeredCarts}");
    }
}
