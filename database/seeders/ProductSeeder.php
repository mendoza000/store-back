<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la store principal
        $store = \App\Models\Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear productos');
            return;
        }

        $categories = \App\Models\Category::where('store_id', $store->id)->get();

        if ($categories->isEmpty()) {
            $this->command->warn('⚠️  No hay categorías disponibles para crear productos');
            return;
        }

        $products = [
            // Electrónicos
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'El iPhone más avanzado con chip A17 Pro, cámara de 48MP y diseño en titanio.',
                'sku' => 'IPH15P-256-BLK',
                'price' => 999.99,
                'stock' => 50,
                'category_id' => $categories->where('name', 'Electrónicos')->first()->id,
                'category' => 'Electrónicos',
                'brand' => 'Apple',
                'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400',
                'metadata' => [
                    'color' => 'Negro',
                    'storage' => '256GB',
                    'screen_size' => '6.1"',
                    'battery' => '3650mAh'
                ]
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'El smartphone más potente de Samsung con S Pen integrado y cámara de 200MP.',
                'sku' => 'SGS24U-512-GRN',
                'price' => 1199.99,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Electrónicos')->first()->id,
                'category' => 'Electrónicos',
                'brand' => 'Samsung',
                'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400',
                'metadata' => [
                    'color' => 'Verde',
                    'storage' => '512GB',
                    'screen_size' => '6.8"',
                    'battery' => '5000mAh'
                ]
            ],
            [
                'name' => 'MacBook Air M3',
                'description' => 'Laptop ultraligera con chip M3, hasta 18 horas de batería y pantalla Liquid Retina.',
                'sku' => 'MBA-M3-512-SLV',
                'price' => 1299.99,
                'stock' => 25,
                'category_id' => $categories->where('name', 'Electrónicos')->first()->id,
                'category' => 'Electrónicos',
                'brand' => 'Apple',
                'image' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400',
                'metadata' => [
                    'color' => 'Plateado',
                    'storage' => '512GB',
                    'ram' => '8GB',
                    'screen_size' => '13.6"'
                ]
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Auriculares inalámbricos con cancelación de ruido líder en la industria.',
                'sku' => 'SONY-WH5-BLK',
                'price' => 349.99,
                'stock' => 40,
                'category_id' => $categories->where('name', 'Electrónicos')->first()->id,
                'category' => 'Electrónicos',
                'brand' => 'Sony',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400',
                'metadata' => [
                    'color' => 'Negro',
                    'battery_life' => '30 horas',
                    'noise_cancellation' => true,
                    'bluetooth' => '5.2'
                ]
            ],
            [
                'name' => 'iPad Air 5th Gen',
                'description' => 'iPad versátil con chip M1, pantalla Liquid Retina y compatibilidad con Apple Pencil.',
                'sku' => 'IPAD-AIR5-256-BLU',
                'price' => 699.99,
                'stock' => 35,
                'category_id' => $categories->where('name', 'Electrónicos')->first()->id,
                'category' => 'Electrónicos',
                'brand' => 'Apple',
                'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400',
                'metadata' => [
                    'color' => 'Azul',
                    'storage' => '256GB',
                    'screen_size' => '10.9"',
                    'cellular' => false
                ]
            ],

            // Ropa
            [
                'name' => 'Camisa Oxford Clásica',
                'description' => 'Camisa de algodón Oxford, perfecta para ocasiones casuales y formales.',
                'sku' => 'CAM-OXF-WHT-M',
                'price' => 45.99,
                'stock' => 60,
                'category_id' => $categories->where('name', 'Ropa')->first()->id,
                'category' => 'Ropa',
                'brand' => 'Classic Wear',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400',
                'metadata' => [
                    'color' => 'Blanco',
                    'size' => 'M',
                    'material' => 'Algodón Oxford',
                    'fit' => 'Regular'
                ]
            ],
            [
                'name' => 'Jeans Slim Fit Premium',
                'description' => 'Jeans de alta calidad con stretch, perfectos para un look moderno y cómodo.',
                'sku' => 'JEA-SLIM-BLU-32',
                'price' => 79.99,
                'stock' => 45,
                'category_id' => $categories->where('name', 'Ropa')->first()->id,
                'category' => 'Ropa',
                'brand' => 'Denim Co.',
                'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400',
                'metadata' => [
                    'color' => 'Azul',
                    'size' => '32',
                    'fit' => 'Slim',
                    'material' => 'Denim con stretch'
                ]
            ],
            [
                'name' => 'Vestido Floral Veraniego',
                'description' => 'Vestido ligero con estampado floral, ideal para días soleados y eventos casuales.',
                'sku' => 'VES-FLOR-MULT-S',
                'price' => 65.99,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Ropa')->first()->id,
                'category' => 'Ropa',
                'brand' => 'Summer Style',
                'image' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=400',
                'metadata' => [
                    'color' => 'Multicolor',
                    'size' => 'S',
                    'material' => 'Algodón',
                    'pattern' => 'Floral'
                ]
            ],
            [
                'name' => 'Chaqueta Bomber Clásica',
                'description' => 'Chaqueta bomber de cuero sintético, perfecta para añadir estilo a cualquier outfit.',
                'sku' => 'CHA-BOMB-BLK-M',
                'price' => 89.99,
                'stock' => 25,
                'category_id' => $categories->where('name', 'Ropa')->first()->id,
                'category' => 'Ropa',
                'brand' => 'Urban Outfitters',
                'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400',
                'metadata' => [
                    'color' => 'Negro',
                    'size' => 'M',
                    'material' => 'Cuero sintético',
                    'style' => 'Bomber'
                ]
            ],

            // Hogar
            [
                'name' => 'Lámpara de Mesa LED',
                'description' => 'Lámpara de mesa moderna con tecnología LED, perfecta para escritorio o mesita de noche.',
                'sku' => 'LAM-LED-DESK-WHT',
                'price' => 39.99,
                'stock' => 40,
                'category_id' => $categories->where('name', 'Hogar')->first()->id,
                'category' => 'Hogar',
                'brand' => 'Home Light',
                'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=400',
                'metadata' => [
                    'color' => 'Blanco',
                    'power' => '10W',
                    'brightness' => '800 lumens',
                    'adjustable' => true
                ]
            ],
            [
                'name' => 'Juego de Sábanas Premium',
                'description' => 'Sábanas de algodón egipcio de 400 hilos, suaves y transpirables.',
                'sku' => 'SAB-PREM-WHT-QUEEN',
                'price' => 89.99,
                'stock' => 35,
                'category_id' => $categories->where('name', 'Hogar')->first()->id,
                'category' => 'Hogar',
                'brand' => 'Luxury Bedding',
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=400',
                'metadata' => [
                    'color' => 'Blanco',
                    'size' => 'Queen',
                    'thread_count' => '400',
                    'material' => 'Algodón egipcio'
                ]
            ],
            [
                'name' => 'Cafetera Programable',
                'description' => 'Cafetera automática con programador, molinillo integrado y capacidad para 12 tazas.',
                'sku' => 'CAF-PROG-12CUP-BLK',
                'price' => 129.99,
                'stock' => 20,
                'category_id' => $categories->where('name', 'Hogar')->first()->id,
                'category' => 'Hogar',
                'brand' => 'Coffee Master',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400',
                'metadata' => [
                    'color' => 'Negro',
                    'capacity' => '12 tazas',
                    'programmable' => true,
                    'grinder' => true
                ]
            ],
            [
                'name' => 'Almohadas Ortopédicas',
                'description' => 'Almohadas de memoria viscoelástica, diseñadas para un descanso óptimo.',
                'sku' => 'ALM-ORTO-MEMORY-STD',
                'price' => 49.99,
                'stock' => 50,
                'category_id' => $categories->where('name', 'Hogar')->first()->id,
                'category' => 'Hogar',
                'brand' => 'Sleep Comfort',
                'image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400',
                'metadata' => [
                    'size' => 'Estándar',
                    'material' => 'Memoria viscoelástica',
                    'firmness' => 'Media',
                    'hypoallergenic' => true
                ]
            ],

            // Deportes
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Zapatillas deportivas con tecnología Air Max, perfectas para running y lifestyle.',
                'sku' => 'NKE-AIR270-WHT-42',
                'price' => 129.99,
                'stock' => 45,
                'category_id' => $categories->where('name', 'Deportes')->first()->id,
                'category' => 'Deportes',
                'brand' => 'Nike',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
                'metadata' => [
                    'color' => 'Blanco',
                    'size' => '42',
                    'type' => 'Running',
                    'technology' => 'Air Max'
                ]
            ],
            [
                'name' => 'Yoga Mat Premium',
                'description' => 'Mat de yoga de goma natural, antideslizante y con líneas de alineación.',
                'sku' => 'YOG-MAT-BLU-PREMIUM',
                'price' => 39.99,
                'stock' => 80,
                'category_id' => $categories->where('name', 'Deportes')->first()->id,
                'category' => 'Deportes',
                'brand' => 'Yoga Life',
                'image' => 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400',
                'metadata' => [
                    'color' => 'Azul',
                    'thickness' => '6mm',
                    'material' => 'Goma natural',
                    'non_slip' => true
                ]
            ],
            [
                'name' => 'Dumbbells Ajustables',
                'description' => 'Pesas ajustables de 2.5 a 25kg, perfectas para entrenamiento en casa.',
                'sku' => 'DUM-ADJ-25KG-PAIR',
                'price' => 149.99,
                'stock' => 15,
                'category_id' => $categories->where('name', 'Deportes')->first()->id,
                'category' => 'Deportes',
                'brand' => 'FitPro',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400',
                'metadata' => [
                    'weight_range' => '2.5-25kg',
                    'type' => 'Ajustables',
                    'material' => 'Hierro fundido',
                    'grip' => 'Antideslizante'
                ]
            ],

            // Libros
            [
                'name' => 'El Señor de los Anillos - Trilogía Completa',
                'description' => 'Edición especial de la trilogía completa de Tolkien con mapas y notas.',
                'sku' => 'LIB-LOTR-TRILOGY-HC',
                'price' => 49.99,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Libros')->first()->id,
                'category' => 'Libros',
                'brand' => 'Tolkien Books',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400',
                'metadata' => [
                    'format' => 'Tapa dura',
                    'pages' => 1200,
                    'language' => 'Español',
                    'includes_maps' => true
                ]
            ],
            [
                'name' => 'Cocina Mediterránea - Recetas Tradicionales',
                'description' => 'Libro de cocina con 150 recetas auténticas de la cocina mediterránea.',
                'sku' => 'LIB-MED-COOK-SOFT',
                'price' => 29.99,
                'stock' => 50,
                'category_id' => $categories->where('name', 'Libros')->first()->id,
                'category' => 'Libros',
                'brand' => 'Culinary Arts',
                'image' => 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400',
                'metadata' => [
                    'format' => 'Tapa blanda',
                    'pages' => 320,
                    'recipes' => 150,
                    'cuisine' => 'Mediterránea'
                ]
            ]
        ];

        foreach ($products as $productData) {
            // Generar slug automáticamente
            $productData['slug'] = Str::slug($productData['name']);
            // Asignar store_id
            $productData['store_id'] = $store->id;

            Product::create($productData);
        }

        $this->command->info('✅ Productos creados exitosamente: ' . count($products) . ' productos para store: ' . $store->name);
    }
}
