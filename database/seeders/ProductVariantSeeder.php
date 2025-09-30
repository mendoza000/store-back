<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la store principal
        $store = \App\Models\Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear variantes de productos');
            return;
        }

        // Obtener todos los productos de la store
        $products = Product::where('store_id', $store->id)->get();

        if ($products->isEmpty()) {
            $this->command->warn('⚠️  No hay productos disponibles para crear variantes');
            return;
        }

        $totalVariants = 0;

        // Definir variantes posibles por categoría
        $variantsByCategory = [
            'Zapatos Deportivos' => [
                ['name' => 'Talla', 'values' => ['38', '39', '40', '41', '42', '43', '44', '45']],
                ['name' => 'Color', 'values' => ['Negro', 'Blanco', 'Azul', 'Rojo', 'Gris', 'Verde']],
            ],
            'Zapatos Casuales' => [
                ['name' => 'Talla', 'values' => ['37', '38', '39', '40', '41', '42', '43', '44']],
                ['name' => 'Color', 'values' => ['Negro', 'Blanco', 'Beige', 'Azul marino', 'Café']],
            ],
            'Zapatos Formales' => [
                ['name' => 'Talla', 'values' => ['39', '40', '41', '42', '43', '44', '45']],
                ['name' => 'Color', 'values' => ['Negro', 'Marrón', 'Café', 'Vino']],
            ],
            'Botas' => [
                ['name' => 'Talla', 'values' => ['38', '39', '40', '41', '42', '43', '44', '45']],
                ['name' => 'Color', 'values' => ['Negro', 'Marrón', 'Trigo', 'Café oscuro']],
            ],
            'Sandalias' => [
                ['name' => 'Talla', 'values' => ['36', '37', '38', '39', '40', '41', '42', '43']],
                ['name' => 'Color', 'values' => ['Negro', 'Marrón', 'Beige', 'Blanco', 'Azul']],
            ],
            'Zapatos para Niños' => [
                ['name' => 'Talla', 'values' => ['24', '25', '26', '27', '28', '29', '30', '31', '32']],
                ['name' => 'Color', 'values' => ['Rojo', 'Azul', 'Rosa', 'Negro', 'Blanco', 'Multicolor']],
            ],
        ];

        foreach ($products as $product) {
            // Determinar las variantes según la categoría del producto
            $category = $product->category ?? 'General';
            $variants = $variantsByCategory[$category] ?? [
                ['name' => 'Tamaño', 'values' => ['S', 'M', 'L']],
                ['name' => 'Color', 'values' => ['Rojo', 'Azul', 'Verde']],
            ];

            // Seleccionar 1 o 2 tipos de variantes para este producto
            $selectedVariants = array_rand($variants, min(2, count($variants)));
            
            // Si solo hay un tipo de variante, array_rand devuelve un número, no un array
            if (!is_array($selectedVariants)) {
                $selectedVariants = [$selectedVariants];
            }

            foreach ($selectedVariants as $variantIndex) {
                $variant = $variants[$variantIndex];
                
                // Crear 2-4 valores de variante de los disponibles
                $selectedValues = array_rand(array_flip($variant['values']), min(rand(2, 4), count($variant['values'])));
                
                if (!is_array($selectedValues)) {
                    $selectedValues = [$selectedValues];
                }

                foreach ($selectedValues as $value) {
                    // Calcular precio de la variante (puede ser el mismo o un poco diferente)
                    $basePrice = $product->price;
                    $priceVariation = rand(-10, 20) / 100; // -10% a +20%
                    $variantPrice = round($basePrice * (1 + $priceVariation), 2);

                    ProductVariant::create([
                        'id' => Str::uuid(),
                        'product_id' => $product->id,
                        'variant_name' => $variant['name'],
                        'variant_value' => $value,
                        'price' => $variantPrice,
                        'compare_price' => rand(0, 1) ? round($variantPrice * 1.2, 2) : null,
                        'stock' => rand(0, 50),
                        'sku' => strtoupper(substr($product->sku, 0, 5)) . '-' . strtoupper(substr($value, 0, 3)) . '-' . Str::random(4),
                        'status' => ['active', 'active', 'active', 'inactive', 'out_of_stock'][rand(0, 4)], // Mayor probabilidad de active
                    ]);
                    
                    $totalVariants++;
                }
            }
        }

        $this->command->info("✅ Variantes creadas exitosamente: {$totalVariants} variantes para {$products->count()} productos de la store: {$store->name}");
    }
} 