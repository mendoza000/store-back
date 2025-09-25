<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Obtener la store principal
        $store = \App\Models\Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear categorías');
            return;
        }

        $categories = [
            [
                'name' => 'Electrónicos',
                'slug' => 'electronicos',
                'description' => 'Productos electrónicos y tecnológicos',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ropa',
                'slug' => 'ropa',
                'description' => 'Ropa y accesorios de moda',
                'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Hogar',
                'slug' => 'hogar',
                'description' => 'Productos para el hogar y decoración',
                'image' => 'https://images.unsplash.com/photo-1484101403633-562f891dc89a?w=400',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Deportes',
                'slug' => 'deportes',
                'description' => 'Equipos y ropa deportiva',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'name' => 'Libros',
                'slug' => 'libros',
                'description' => 'Libros y material de lectura',
                'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400',
                'status' => 'active',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['store_id'] = $store->id;
            Category::create($categoryData);
        }

        $this->command->info('✅ Categorías creadas exitosamente: ' . count($categories) . ' categorías para store: ' . $store->name);
    }
}
