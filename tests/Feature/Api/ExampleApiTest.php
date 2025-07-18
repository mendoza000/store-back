<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica el endpoint de salud de la API
     */
    public function test_api_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'timestamp',
                'version'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'API is running',
                'version' => '1.0.0'
            ]);
    }

    /**
     * Test que verifica el endpoint de configuración pública
     */
    public function test_api_config_endpoint(): void
    {
        $response = $this->getJson('/api/v1/config');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'app_name',
                    'modules' => [
                        'coupons',
                        'product_variants',
                        'wishlist',
                        'reviews'
                    ],
                    'features' => [
                        'multi_payment_methods',
                        'notifications'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    /**
     * Test que verifica el endpoint de ejemplos (lista)
     */
    public function test_example_index_endpoint(): void
    {
        $response = $this->getJson('/api/v1/example');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'created_at'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Ejemplos obtenidos exitosamente'
            ]);
    }

    /**
     * Test que verifica el endpoint de ejemplo específico
     */
    public function test_example_show_endpoint(): void
    {
        $response = $this->getJson('/api/v1/example/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Ejemplo obtenido exitosamente',
                'data' => [
                    'id' => 1,
                    'name' => 'Ejemplo 1'
                ]
            ]);
    }

    /**
     * Test que verifica respuesta 404 para ejemplo no encontrado
     */
    public function test_example_not_found(): void
    {
        $response = $this->getJson('/api/v1/example/999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message'
                ]
            ])
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Ejemplo no encontrado'
                ]
            ]);
    }

    /**
     * Test que verifica validación en endpoint de ejemplos
     */
    public function test_example_validation(): void
    {
        $response = $this->getJson('/api/v1/example?page=0&per_page=101');

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    /**
     * Test que verifica el endpoint fallback para rutas no encontradas
     */
    public function test_api_fallback_route(): void
    {
        $response = $this->getJson('/api/v1/non-existent-endpoint');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'error' => [
                    'code',
                    'message',
                    'details' => [
                        'requested_url',
                        'method'
                    ]
                ]
            ])
            ->assertJson([
                'success' => false,
                'error' => [
                    'code' => 'ENDPOINT_NOT_FOUND',
                    'message' => 'The requested API endpoint was not found.'
                ]
            ]);
    }

    /**
     * Test que verifica la estructura de respuesta estándar
     */
    public function test_standard_api_response_structure(): void
    {
        $response = $this->getJson('/api/v1/example');

        // Verificar que la respuesta sigue el estándar definido
        $this->assertTrue($response->json('success'));
        $this->assertNotNull($response->json('message'));
        $this->assertNotNull($response->json('data'));

        // Verificar que los datos son un array
        $this->assertIsArray($response->json('data'));
    }
}
