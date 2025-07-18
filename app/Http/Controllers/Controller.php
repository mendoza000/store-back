<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="E-commerce API",
 *     version="1.0.0",
 *     description="API REST para sistema de e-commerce con autenticación y gestión de recursos.",
 *     @OA\Contact(
 *         email="api@ecommerce.com",
 *         name="E-commerce API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     in="header",
 *     name="Authorization",
 *     description="Token de autenticación Sanctum. Formato: 'Bearer {token}'"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints de autenticación y gestión de usuarios"
 * )
 * 
 * @OA\Tag(
 *     name="Products",
 *     description="Gestión de productos, categorías y variantes"
 * )
 * 
 * @OA\Tag(
 *     name="Cart",
 *     description="Carrito de compras y gestión de items"
 * )
 * 
 * @OA\Tag(
 *     name="Orders",
 *     description="Gestión de pedidos y estados"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Procesamiento de pagos y comprobantes"
 * )
 * 
 * @OA\Tag(
 *     name="Coupons",
 *     description="Sistema de cupones y descuentos (módulo opcional)"
 * )
 * 
 * @OA\Tag(
 *     name="Admin",
 *     description="Endpoints de administración"
 * )
 * 

 * 

 */
abstract class Controller
{
    //
}
