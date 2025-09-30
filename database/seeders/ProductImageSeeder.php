<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la store principal
        $store = \App\Models\Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear imágenes de productos');
            return;
        }

        // Obtener todos los productos de la store
        $products = Product::where('store_id', $store->id)->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  No hay productos disponibles para crear imágenes');
            return;
        }

        $totalImages = 0;

        foreach ($products as $product) {
            // Crear entre 2 y 5 imágenes por producto
            $imageCount = rand(2, 5);

            for ($i = 0; $i < $imageCount; $i++) {
                ProductImage::create([
                    'id' => Str::uuid(),
                    'product_id' => $product->id,
                    'image_path' => 'products/' . Str::random(40) . '.jpg',
                    'url' => 'https://images.unsplash.com/photo-' . rand(1500000000000, 1600000000000) . '?w=800',
                    'sort_order' => $i,
                    'is_primary' => $i === 0, // La primera imagen es la principal
                    'is_active' => true,
                    'alt_text' => $product->name . ' - Imagen ' . ($i + 1),
                    'title' => 'Foto de ' . $product->name,
                ]);
                
                $totalImages++;
            }
        }

        $this->command->info("✅ Imágenes creadas exitosamente: {$totalImages} imágenes para {$products->count()} productos de la store: {$store->name}");
    }
} 