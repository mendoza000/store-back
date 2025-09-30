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
                'name' => 'Zapatos Deportivos',
                'slug' => 'zapatos-deportivos',
                'description' => 'Zapatillas y calzado deportivo para correr, entrenar y actividades físicas',
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Zapatos Casuales',
                'slug' => 'zapatos-casuales',
                'description' => 'Calzado casual para el día a día, cómodo y con estilo',
                'image' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=400',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Zapatos Formales',
                'slug' => 'zapatos-formales',
                'description' => 'Calzado elegante para eventos formales y oficina',
                'image' => 'https://images.unsplash.com/photo-1614252235316-8c857d38b5f4?w=400',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Botas',
                'slug' => 'botas',
                'description' => 'Botas de trabajo, senderismo y estilo urbano',
                'image' => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=400',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'name' => 'Sandalias',
                'slug' => 'sandalias',
                'description' => 'Sandalias y chanclas para verano y playa',
                'image' => 'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=400',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'name' => 'Zapatos para Niños',
                'slug' => 'zapatos-para-ninos',
                'description' => 'Calzado infantil cómodo y duradero',
                'image' => 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?w=400',
                'status' => 'active',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            $categoryData['store_id'] = $store->id;
            Category::create($categoryData);
        }

        $this->command->info('✅ Categorías creadas exitosamente: ' . count($categories) . ' categorías para store: ' . $store->name);
    }
}
