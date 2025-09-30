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
            // Zapatos Deportivos
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Zapatillas deportivas con amortiguación Air Max visible, diseñadas para máximo confort durante todo el día. Ideal para running y uso casual.',
                'short_description' => 'Zapatillas deportivas con tecnología Air Max',
                'sku' => 'NK-AM270-BLK-42',
                'price' => 129.99,
                'compare_price' => 159.99,
                'cost_price' => 85.00,
                'stock' => 45,
                'category_id' => $categories->where('name', 'Zapatos Deportivos')->first()->id,
                'category' => 'Zapatos Deportivos',
                'brand' => 'Nike',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro',
                    'size' => '42',
                    'material' => 'Mesh transpirable',
                    'suela' => 'Caucho',
                    'tipo' => 'Running'
                ]
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Zapatillas de running con tecnología Boost para máximo retorno de energía. Parte superior Primeknit para ajuste perfecto.',
                'short_description' => 'Running shoes con tecnología Boost',
                'sku' => 'AD-UB22-WHT-41',
                'price' => 149.99,
                'compare_price' => 180.00,
                'cost_price' => 95.00,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Zapatos Deportivos')->first()->id,
                'category' => 'Zapatos Deportivos',
                'brand' => 'Adidas',
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Blanco',
                    'size' => '41',
                    'material' => 'Primeknit',
                    'suela' => 'Boost',
                    'tipo' => 'Running'
                ]
            ],
            [
                'name' => 'Puma RS-X',
                'description' => 'Zapatillas deportivas estilo retro con diseño chunky y colores vibrantes. Perfectas para estilo urbano y actividades casuales.',
                'short_description' => 'Sneakers estilo retro chunky',
                'sku' => 'PM-RSX-MLT-40',
                'price' => 99.99,
                'compare_price' => 120.00,
                'cost_price' => 65.00,
                'stock' => 50,
                'category_id' => $categories->where('name', 'Zapatos Deportivos')->first()->id,
                'category' => 'Zapatos Deportivos',
                'brand' => 'Puma',
                'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Multicolor',
                    'size' => '40',
                    'material' => 'Sintético y mesh',
                    'suela' => 'Caucho',
                    'tipo' => 'Lifestyle'
                ]
            ],
            [
                'name' => 'New Balance 574',
                'description' => 'Zapatillas clásicas con diseño atemporal. Comodidad excepcional con amortiguación ENCAP y estilo versátil.',
                'short_description' => 'Zapatillas clásicas New Balance',
                'sku' => 'NB-574-GRY-43',
                'price' => 89.99,
                'compare_price' => 110.00,
                'cost_price' => 55.00,
                'stock' => 60,
                'category_id' => $categories->where('name', 'Zapatos Deportivos')->first()->id,
                'category' => 'Zapatos Deportivos',
                'brand' => 'New Balance',
                'image' => 'https://images.unsplash.com/photo-1539185441755-769473a23570?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Gris',
                    'size' => '43',
                    'material' => 'Gamuza y mesh',
                    'suela' => 'ENCAP',
                    'tipo' => 'Casual'
                ]
            ],

            // Zapatos Casuales
            [
                'name' => 'Converse Chuck Taylor All Star',
                'description' => 'Zapatillas icónicas de lona con diseño clásico. Perfectas para cualquier outfit casual y estilo urbano.',
                'short_description' => 'Zapatillas de lona clásicas Converse',
                'sku' => 'CV-CTAS-BLK-39',
                'price' => 59.99,
                'compare_price' => 75.00,
                'cost_price' => 35.00,
                'stock' => 80,
                'category_id' => $categories->where('name', 'Zapatos Casuales')->first()->id,
                'category' => 'Zapatos Casuales',
                'brand' => 'Converse',
                'image' => 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro',
                    'size' => '39',
                    'material' => 'Lona',
                    'suela' => 'Caucho',
                    'tipo' => 'Low top'
                ]
            ],
            [
                'name' => 'Vans Old Skool',
                'description' => 'Zapatillas skate clásicas con la icónica raya lateral. Diseño duradero con suela waffle grip.',
                'short_description' => 'Zapatillas skate clásicas Vans',
                'sku' => 'VN-OS-BKWH-41',
                'price' => 69.99,
                'compare_price' => 85.00,
                'cost_price' => 40.00,
                'stock' => 70,
                'category_id' => $categories->where('name', 'Zapatos Casuales')->first()->id,
                'category' => 'Zapatos Casuales',
                'brand' => 'Vans',
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro/Blanco',
                    'size' => '41',
                    'material' => 'Lona y gamuza',
                    'suela' => 'Waffle grip',
                    'tipo' => 'Skate'
                ]
            ],
            [
                'name' => 'Skechers Go Walk',
                'description' => 'Zapatillas ultraligeras y cómodas para caminar. Tecnología Goga Mat para máxima comodidad durante todo el día.',
                'short_description' => 'Zapatillas ligeras para caminar',
                'sku' => 'SK-GW-NVY-42',
                'price' => 79.99,
                'compare_price' => 95.00,
                'cost_price' => 48.00,
                'stock' => 55,
                'category_id' => $categories->where('name', 'Zapatos Casuales')->first()->id,
                'category' => 'Zapatos Casuales',
                'brand' => 'Skechers',
                'image' => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Azul marino',
                    'size' => '42',
                    'material' => 'Mesh transpirable',
                    'suela' => 'Goga Mat',
                    'tipo' => 'Walking'
                ]
            ],

            // Zapatos Formales
            [
                'name' => 'Zapatos Oxford de Cuero',
                'description' => 'Zapatos Oxford clásicos en cuero genuino. Perfectos para eventos formales, oficina y ocasiones especiales.',
                'short_description' => 'Oxford de cuero genuino',
                'sku' => 'OXF-LTH-BLK-42',
                'price' => 119.99,
                'compare_price' => 150.00,
                'cost_price' => 70.00,
                'stock' => 35,
                'category_id' => $categories->where('name', 'Zapatos Formales')->first()->id,
                'category' => 'Zapatos Formales',
                'brand' => 'Eleganza',
                'image' => 'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro',
                    'size' => '42',
                    'material' => 'Cuero genuino',
                    'suela' => 'Cuero',
                    'tipo' => 'Oxford'
                ]
            ],
            [
                'name' => 'Mocasines de Cuero Premium',
                'description' => 'Mocasines elegantes de cuero italiano. Comodidad sin cordones con estilo sofisticado.',
                'short_description' => 'Mocasines de cuero italiano',
                'sku' => 'MOC-ITA-BRW-41',
                'price' => 139.99,
                'compare_price' => 170.00,
                'cost_price' => 85.00,
                'stock' => 25,
                'category_id' => $categories->where('name', 'Zapatos Formales')->first()->id,
                'category' => 'Zapatos Formales',
                'brand' => 'Italiano Classico',
                'image' => 'https://images.unsplash.com/photo-1533867617858-e7b97e060509?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Marrón',
                    'size' => '41',
                    'material' => 'Cuero italiano',
                    'suela' => 'Cuero',
                    'tipo' => 'Mocasín'
                ]
            ],
            [
                'name' => 'Zapatos Brogue Wingtip',
                'description' => 'Zapatos Brogue con detalle wingtip. Diseño clásico inglés con perforaciones decorativas.',
                'short_description' => 'Brogue Wingtip clásico',
                'sku' => 'BRG-WTP-TAN-43',
                'price' => 129.99,
                'compare_price' => 160.00,
                'cost_price' => 75.00,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Zapatos Formales')->first()->id,
                'category' => 'Zapatos Formales',
                'brand' => 'British Style',
                'image' => 'https://images.unsplash.com/photo-1478460955814-5fcd6b8a8076?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Café',
                    'size' => '43',
                    'material' => 'Cuero',
                    'suela' => 'Cuero',
                    'tipo' => 'Brogue'
                ]
            ],

            // Botas
            [
                'name' => 'Timberland 6-Inch Premium',
                'description' => 'Botas icónicas de trabajo resistentes al agua. Construcción duradera con suela antideslizante.',
                'short_description' => 'Botas de trabajo impermeables',
                'sku' => 'TMB-6IN-WHT-42',
                'price' => 189.99,
                'compare_price' => 220.00,
                'cost_price' => 120.00,
                'stock' => 40,
                'category_id' => $categories->where('name', 'Botas')->first()->id,
                'category' => 'Botas',
                'brand' => 'Timberland',
                'image' => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Trigo',
                    'size' => '42',
                    'material' => 'Cuero nubuck',
                    'suela' => 'Caucho antideslizante',
                    'tipo' => 'Trabajo',
                    'waterproof' => true
                ]
            ],
            [
                'name' => 'Dr. Martens 1460',
                'description' => 'Botas clásicas de 8 ojales con suela AirWair. Estilo icónico resistente y cómodo.',
                'short_description' => 'Botas clásicas Dr. Martens',
                'sku' => 'DRM-1460-BLK-40',
                'price' => 159.99,
                'compare_price' => 190.00,
                'cost_price' => 95.00,
                'stock' => 35,
                'category_id' => $categories->where('name', 'Botas')->first()->id,
                'category' => 'Botas',
                'brand' => 'Dr. Martens',
                'image' => 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro',
                    'size' => '40',
                    'material' => 'Cuero',
                    'suela' => 'AirWair',
                    'tipo' => 'Urbano',
                    'ojales' => 8
                ]
            ],
            [
                'name' => 'Botas Chelsea de Cuero',
                'description' => 'Botas Chelsea elegantes con elástico lateral. Fáciles de poner y perfectas para look casual-formal.',
                'short_description' => 'Chelsea boots de cuero',
                'sku' => 'CHL-LTH-BRW-41',
                'price' => 139.99,
                'compare_price' => 170.00,
                'cost_price' => 80.00,
                'stock' => 30,
                'category_id' => $categories->where('name', 'Botas')->first()->id,
                'category' => 'Botas',
                'brand' => 'Urban Boots',
                'image' => 'https://images.unsplash.com/photo-1605812860427-4024433a70fd?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Marrón',
                    'size' => '41',
                    'material' => 'Cuero',
                    'suela' => 'Caucho',
                    'tipo' => 'Chelsea'
                ]
            ],

            // Sandalias
            [
                'name' => 'Birkenstock Arizona',
                'description' => 'Sandalias ortopédicas con doble correa ajustable. Plantilla de corcho moldeada para máximo confort.',
                'short_description' => 'Sandalias ortopédicas de corcho',
                'sku' => 'BKS-ARZ-BLK-41',
                'price' => 89.99,
                'compare_price' => 110.00,
                'cost_price' => 50.00,
                'stock' => 50,
                'category_id' => $categories->where('name', 'Sandalias')->first()->id,
                'category' => 'Sandalias',
                'brand' => 'Birkenstock',
                'image' => 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Negro',
                    'size' => '41',
                    'material' => 'Piel sintética',
                    'plantilla' => 'Corcho',
                    'tipo' => 'Ortopédica'
                ]
            ],
            [
                'name' => 'Havaianas Brasil',
                'description' => 'Chanclas brasileñas clásicas de caucho. Ligeras, cómodas y perfectas para playa o piscina.',
                'short_description' => 'Chanclas brasileñas de caucho',
                'sku' => 'HAV-BRA-GRN-39',
                'price' => 24.99,
                'compare_price' => 30.00,
                'cost_price' => 12.00,
                'stock' => 100,
                'category_id' => $categories->where('name', 'Sandalias')->first()->id,
                'category' => 'Sandalias',
                'brand' => 'Havaianas',
                'image' => 'https://images.unsplash.com/photo-1580342789696-31a0c9dd93bb?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Verde/Amarillo',
                    'size' => '39-40',
                    'material' => 'Caucho',
                    'tipo' => 'Chancla',
                    'uso' => 'Playa'
                ]
            ],
            [
                'name' => 'Sandalias Deportivas Teva',
                'description' => 'Sandalias deportivas con correas ajustables. Ideales para senderismo y actividades acuáticas.',
                'short_description' => 'Sandalias deportivas para outdoor',
                'sku' => 'TVA-SPT-BLU-42',
                'price' => 69.99,
                'compare_price' => 85.00,
                'cost_price' => 40.00,
                'stock' => 45,
                'category_id' => $categories->where('name', 'Sandalias')->first()->id,
                'category' => 'Sandalias',
                'brand' => 'Teva',
                'image' => 'https://images.unsplash.com/photo-1621616726922-35629e98de55?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Azul',
                    'size' => '42',
                    'material' => 'Nylon',
                    'suela' => 'Caucho',
                    'tipo' => 'Deportiva',
                    'uso' => 'Outdoor'
                ]
            ],

            // Zapatos para Niños
            [
                'name' => 'Zapatillas Velcro Niños',
                'description' => 'Zapatillas infantiles con cierre de velcro. Fáciles de poner y perfectas para el día a día.',
                'short_description' => 'Zapatillas infantiles con velcro',
                'sku' => 'KDS-VEL-RED-28',
                'price' => 39.99,
                'compare_price' => 50.00,
                'cost_price' => 20.00,
                'stock' => 60,
                'category_id' => $categories->where('name', 'Zapatos para Niños')->first()->id,
                'category' => 'Zapatos para Niños',
                'brand' => 'Kids Footwear',
                'image' => 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Rojo',
                    'size' => '28',
                    'material' => 'Sintético',
                    'cierre' => 'Velcro',
                    'edad' => '4-6 años'
                ]
            ],
            [
                'name' => 'Zapatillas LED para Niños',
                'description' => 'Zapatillas con luces LED que se activan al caminar. Diversión garantizada para los más pequeños.',
                'short_description' => 'Zapatillas con luces LED',
                'sku' => 'KDS-LED-BLU-30',
                'price' => 49.99,
                'compare_price' => 65.00,
                'cost_price' => 25.00,
                'stock' => 55,
                'category_id' => $categories->where('name', 'Zapatos para Niños')->first()->id,
                'category' => 'Zapatos para Niños',
                'brand' => 'Light Up Kids',
                'image' => 'https://images.unsplash.com/photo-1560072810-1cffb09faf0f?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Azul',
                    'size' => '30',
                    'material' => 'Sintético',
                    'caracteristica' => 'Luces LED',
                    'edad' => '6-8 años'
                ]
            ],
            [
                'name' => 'Botas de Lluvia Infantiles',
                'description' => 'Botas de lluvia impermeables para niños. Diseño divertido y colorido, perfectas para días lluviosos.',
                'short_description' => 'Botas de lluvia para niños',
                'sku' => 'KDS-RBT-YLW-26',
                'price' => 34.99,
                'compare_price' => 45.00,
                'cost_price' => 18.00,
                'stock' => 70,
                'category_id' => $categories->where('name', 'Zapatos para Niños')->first()->id,
                'category' => 'Zapatos para Niños',
                'brand' => 'Rain Kids',
                'image' => 'https://images.unsplash.com/photo-1518894781321-630e638d0742?w=400',
                'status' => 'active',
                'track_quantity' => 1,
                'metadata' => [
                    'color' => 'Amarillo',
                    'size' => '26',
                    'material' => 'PVC',
                    'waterproof' => true,
                    'edad' => '3-5 años'
                ]
            ],
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
