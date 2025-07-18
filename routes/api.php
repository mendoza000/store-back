<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
})->name('api.health');

// API Version 1 Routes
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Public configuration endpoint
    Route::get('/config', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => config('app.name'),
                'modules' => [
                    'coupons' => config('modules.coupons', false),
                    'product_variants' => config('modules.product_variants', true),
                    'wishlist' => config('modules.wishlist', false),
                    'reviews' => config('modules.reviews', false),
                ],
                'features' => [
                    'multi_payment_methods' => config('modules.multi_payment_methods', true),
                    'notifications' => config('modules.notifications', [])
                ]
            ]
        ]);
    })->name('config');

    // Authentication routes (no authentication required)
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])
            ->name('login');

        Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register'])
            ->name('register');

        Route::post('/forgot-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword'])
            ->name('forgot-password');

        Route::post('/reset-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword'])
            ->name('reset-password');
    });

    // Example routes (for documentation testing)
    Route::prefix('example')->name('example.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\ExampleController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\V1\ExampleController::class, 'show']);

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/protected', [\App\Http\Controllers\Api\V1\ExampleController::class, 'createProtected']);
            Route::post('/admin', [\App\Http\Controllers\Api\V1\ExampleController::class, 'createAdmin'])
                ->middleware('role:admin');
        });
    });

    // Public product routes (no authentication required)
    Route::prefix('products')->name('products.')->group(function () {
        // GET /api/v1/products - Lista paginada con filtros
        // GET /api/v1/products/{slug} - Detalle de producto
        // GET /api/v1/products/{id}/variants - Variantes del producto
        // GET /api/v1/products/search - Búsqueda de productos
        // GET /api/v1/products/featured - Productos destacados
    });

    // Public category routes
    Route::prefix('categories')->name('categories.')->group(function () {
        // GET /api/v1/categories - Árbol de categorías
        // GET /api/v1/categories/{slug} - Detalle de categoría
        // GET /api/v1/categories/{slug}/products - Productos por categoría
    });

    // Public payment methods
    Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
        // GET /api/v1/payment-methods - Métodos de pago disponibles
        // GET /api/v1/payment-methods/{id} - Detalle del método
    });

    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // User profile routes
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])
                ->name('logout');

            Route::post('/refresh', [\App\Http\Controllers\Api\V1\AuthController::class, 'refresh'])
                ->name('refresh');

            Route::get('/me', [\App\Http\Controllers\Api\V1\AuthController::class, 'me'])
                ->name('me');

            Route::put('/profile', [\App\Http\Controllers\Api\V1\AuthController::class, 'updateProfile'])
                ->name('profile.update');

            Route::post('/change-password', [\App\Http\Controllers\Api\V1\AuthController::class, 'changePassword'])
                ->name('change-password');
        });

        // Cart routes
        Route::prefix('cart')->name('cart.')->group(function () {
            // GET /api/v1/cart
            // POST /api/v1/cart/items
            // PUT /api/v1/cart/items/{id}
            // DELETE /api/v1/cart/items/{id}
            // DELETE /api/v1/cart
            // POST /api/v1/cart/merge

            // Coupon routes (conditional based on modules.coupons)
            Route::middleware('module:coupons')->group(function () {
                // POST /api/v1/cart/apply-coupon
                // DELETE /api/v1/cart/coupon
            });
        });

        // Order routes
        Route::prefix('orders')->name('orders.')->group(function () {
            // POST /api/v1/orders
            // GET /api/v1/orders
            // GET /api/v1/orders/{orderNumber}
            // PUT /api/v1/orders/{id}/cancel
        });

        // Payment routes
        Route::prefix('payments')->name('payments.')->group(function () {
            // GET /api/v1/payments/{id}
            // PUT /api/v1/payments/{id}
        });

        // Order payment routes
        Route::prefix('orders/{order}/payments')->name('orders.payments.')->group(function () {
            // POST /api/v1/orders/{order}/payments
        });

        // Wishlist routes (conditional based on modules.wishlist)
        Route::middleware('module:wishlist')->prefix('wishlist')->name('wishlist.')->group(function () {
            // GET /api/v1/wishlist
            // POST /api/v1/wishlist
            // DELETE /api/v1/wishlist/{productId}
            // POST /api/v1/wishlist/move-to-cart
        });

        // Product reviews (conditional based on modules.reviews)
        Route::middleware('module:reviews')->group(function () {
            // Reviews for products
            Route::prefix('products/{product}/reviews')->name('products.reviews.')->group(function () {
                // POST /api/v1/products/{product}/reviews
            });

            // Review management
            Route::prefix('reviews')->name('reviews.')->group(function () {
                // PUT /api/v1/reviews/{id}/helpful
            });
        });

        // Coupon validation (conditional based on modules.coupons)
        Route::middleware('module:coupons')->prefix('coupons')->name('coupons.')->group(function () {
            // POST /api/v1/coupons/validate
        });
    });

    // Admin routes (require admin role)
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            // GET /api/v1/admin/dashboard
            // GET /api/v1/admin/dashboard/sales
            // GET /api/v1/admin/dashboard/products
            // GET /api/v1/admin/dashboard/customers
        });

        // User management
        Route::prefix('users')->name('users.')->group(function () {
            // GET /api/v1/admin/users
            // GET /api/v1/admin/users/{id}
            // PUT /api/v1/admin/users/{id}
            // PUT /api/v1/admin/users/{id}/status
            // GET /api/v1/admin/users/{id}/orders
        });

        // Product management
        Route::prefix('products')->name('products.')->group(function () {
            // GET /api/v1/admin/products
            // POST /api/v1/admin/products
            // PUT /api/v1/admin/products/{id}
            // DELETE /api/v1/admin/products/{id}
            // POST /api/v1/admin/products/{id}/images
            // POST /api/v1/admin/products/{id}/variants
        });

        // Category management
        Route::prefix('categories')->name('categories.')->group(function () {
            // Standard CRUD operations
        });

        // Order management
        Route::prefix('orders')->name('orders.')->group(function () {
            // GET /api/v1/admin/orders
            // PUT /api/v1/admin/orders/{id}/status
            // GET /api/v1/admin/orders/stats
        });

        // Payment management
        Route::prefix('payments')->name('payments.')->group(function () {
            // GET /api/v1/admin/payments
            // POST /api/v1/admin/payments/{id}/verify
            // POST /api/v1/admin/payments/{id}/reject
            // GET /api/v1/admin/payments/stats
        });

        // Coupon management (conditional)
        Route::middleware('module:coupons')->prefix('coupons')->name('coupons.')->group(function () {
            // GET /api/v1/admin/coupons
            // POST /api/v1/admin/coupons
            // PUT /api/v1/admin/coupons/{id}
            // DELETE /api/v1/admin/coupons/{id}
            // GET /api/v1/admin/coupons/{id}/usage
        });

        // Review management (conditional)
        Route::middleware('module:reviews')->prefix('reviews')->name('reviews.')->group(function () {
            // GET /api/v1/admin/reviews
        });

        // Inventory management (conditional)
        Route::middleware('module:advanced_inventory')->prefix('inventory')->name('inventory.')->group(function () {
            // GET /api/v1/admin/inventory
            // POST /api/v1/admin/inventory/adjustment
            // GET /api/v1/admin/inventory/movements
            // GET /api/v1/admin/inventory/alerts
        });

        // Settings management
        Route::prefix('settings')->name('settings.')->group(function () {
            // GET /api/v1/admin/settings
            // PUT /api/v1/admin/settings
            // GET /api/v1/admin/settings/modules
            // PUT /api/v1/admin/settings/modules
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            // GET /api/v1/admin/reports/sales
            // GET /api/v1/admin/reports/inventory
            // GET /api/v1/admin/reports/customers
            // POST /api/v1/admin/reports/export
        });
    });

    // Public product reviews (no auth required for reading)
    Route::prefix('products/{product}/reviews')->name('products.reviews.')->group(function () {
        // GET /api/v1/products/{product}/reviews
    });
});

// Catch-all route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'error' => [
            'code' => 'ENDPOINT_NOT_FOUND',
            'message' => 'The requested API endpoint was not found.',
            'details' => [
                'requested_url' => request()->fullUrl(),
                'method' => request()->method()
            ]
        ]
    ], 404);
});
