<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Módulos de la Tienda
    |--------------------------------------------------------------------------
    |
    | Configuración modular para habilitar/deshabilitar características
    | de la tienda. Cada módulo puede ser activado o desactivado según
    | las necesidades del cliente.
    |
    */

    // Información básica de la tienda
    'store' => [
        'name' => env('STORE_NAME', 'Mi Tienda Online'),
        'description' => env('STORE_DESCRIPTION', 'Una tienda en línea completa'),
        'currency' => env('STORE_CURRENCY', 'USD'),
        'timezone' => env('STORE_TIMEZONE', 'America/Caracas'),
        'language' => env('STORE_LANGUAGE', 'es'),
    ],

    // Módulos principales del sistema
    'modules' => [

        // ========== MÓDULOS CORE (Siempre activos) ==========
        'core' => [
            'enabled' => true,
            'features' => [
                'products' => true,
                'categories' => true,
                'orders' => true,
                'users' => true,
                'authentication' => true,
            ]
        ],

        // ========== MÓDULO DE COMERCIO ==========
        'commerce' => [
            'enabled' => env('MODULE_COMMERCE', true),
            'features' => [
                'cart' => true,
                'checkout' => true,
                'product_variants' => env('FEATURE_PRODUCT_VARIANTS', true),
                'inventory_tracking' => env('FEATURE_INVENTORY_TRACKING', true),
                'price_comparison' => env('FEATURE_PRICE_COMPARISON', false),
                'bulk_pricing' => env('FEATURE_BULK_PRICING', false),
            ],
            'settings' => [
                'max_cart_items' => env('CART_MAX_ITEMS', 50),
                'cart_session_minutes' => env('CART_SESSION_MINUTES', 1440), // 24 horas
                'auto_reserve_stock' => env('AUTO_RESERVE_STOCK', true),
                'stock_reservation_minutes' => env('STOCK_RESERVATION_MINUTES', 15),
            ]
        ],

        // ========== MÓDULO DE PAGOS ==========
        'payments' => [
            'enabled' => env('MODULE_PAYMENTS', true),
            'features' => [
                'manual_payments' => env('FEATURE_MANUAL_PAYMENTS', true),
                'bank_transfers' => env('FEATURE_BANK_TRANSFERS', true),
                'mobile_payments' => env('FEATURE_MOBILE_PAYMENTS', true),
                'cash_payments' => env('FEATURE_CASH_PAYMENTS', false),
                'crypto_payments' => env('FEATURE_CRYPTO_PAYMENTS', false),
            ],
            'settings' => [
                'payment_verification_required' => env('PAYMENT_VERIFICATION_REQUIRED', true),
                'auto_approve_payments' => env('AUTO_APPROVE_PAYMENTS', false),
                'payment_timeout_hours' => env('PAYMENT_TIMEOUT_HOURS', 48),
            ]
        ],

        // ========== MÓDULO DE MARKETING ==========
        'marketing' => [
            'enabled' => env('MODULE_MARKETING', true),
            'features' => [
                'coupons' => env('FEATURE_COUPONS', true),
                'discounts' => env('FEATURE_DISCOUNTS', true),
                'promotions' => env('FEATURE_PROMOTIONS', false),
                'loyalty_program' => env('FEATURE_LOYALTY_PROGRAM', false),
                'referral_system' => env('FEATURE_REFERRAL_SYSTEM', false),
            ],
            'settings' => [
                'max_coupon_uses' => env('MAX_COUPON_USES', 1000),
                'coupon_stacking' => env('COUPON_STACKING', false),
                'auto_apply_best_discount' => env('AUTO_APPLY_BEST_DISCOUNT', true),
            ]
        ],

        // ========== MÓDULO DE INVENTARIO ==========
        'inventory' => [
            'enabled' => env('MODULE_INVENTORY', true),
            'features' => [
                'stock_management' => env('FEATURE_STOCK_MANAGEMENT', true),
                'low_stock_alerts' => env('FEATURE_LOW_STOCK_ALERTS', true),
                'stock_history' => env('FEATURE_STOCK_HISTORY', false),
                'advanced_inventory' => env('FEATURE_ADVANCED_INVENTORY', false),
                'multi_warehouse' => env('FEATURE_MULTI_WAREHOUSE', false),
            ],
            'settings' => [
                'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5),
                'auto_disable_out_of_stock' => env('AUTO_DISABLE_OUT_OF_STOCK', true),
                'backorder_allowed' => env('BACKORDER_ALLOWED', false),
            ]
        ],

        // ========== MÓDULO DE USUARIOS ==========
        'users' => [
            'enabled' => true,
            'features' => [
                'customer_registration' => env('FEATURE_CUSTOMER_REGISTRATION', true),
                'user_profiles' => env('FEATURE_USER_PROFILES', true),
                'wishlist' => env('FEATURE_WISHLIST', true),
                'order_history' => env('FEATURE_ORDER_HISTORY', true),
                'user_reviews' => env('FEATURE_USER_REVIEWS', false),
                'social_login' => env('FEATURE_SOCIAL_LOGIN', false),
            ],
            'settings' => [
                'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', false),
                'allow_guest_checkout' => env('ALLOW_GUEST_CHECKOUT', true),
                'min_password_length' => env('MIN_PASSWORD_LENGTH', 8),
            ]
        ],

        // ========== MÓDULO DE NOTIFICACIONES ==========
        'notifications' => [
            'enabled' => env('MODULE_NOTIFICATIONS', true),
            'features' => [
                'email_notifications' => env('FEATURE_EMAIL_NOTIFICATIONS', true),
                'sms_notifications' => env('FEATURE_SMS_NOTIFICATIONS', false),
                'whatsapp_notifications' => env('FEATURE_WHATSAPP_NOTIFICATIONS', false),
                'push_notifications' => env('FEATURE_PUSH_NOTIFICATIONS', false),
            ],
            'settings' => [
                'order_status_emails' => env('ORDER_STATUS_EMAILS', true),
                'payment_confirmation_emails' => env('PAYMENT_CONFIRMATION_EMAILS', true),
                'low_stock_admin_emails' => env('LOW_STOCK_ADMIN_EMAILS', true),
            ]
        ],

        // ========== MÓDULO DE ADMINISTRACIÓN ==========
        'administration' => [
            'enabled' => true,
            'features' => [
                'admin_dashboard' => true,
                'user_management' => true,
                'order_management' => true,
                'product_management' => true,
                'reports' => env('FEATURE_REPORTS', true),
                'advanced_analytics' => env('FEATURE_ADVANCED_ANALYTICS', false),
                'export_data' => env('FEATURE_EXPORT_DATA', true),
            ],
            'settings' => [
                'admin_session_timeout' => env('ADMIN_SESSION_TIMEOUT', 120), // minutos
                'require_admin_2fa' => env('REQUIRE_ADMIN_2FA', false),
                'audit_trail' => env('AUDIT_TRAIL', true),
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dependencias entre Módulos
    |--------------------------------------------------------------------------
    |
    | Define qué módulos dependen de otros para funcionar correctamente
    |
    */
    'dependencies' => [
        'commerce' => ['core'],
        'payments' => ['core', 'commerce'],
        'marketing' => ['core', 'commerce'],
        'inventory' => ['core', 'commerce'],
        'notifications' => ['core'],
        'administration' => ['core'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para características en desarrollo
    | o experimentales
    |
    */
    'feature_flags' => [
        'new_checkout_flow' => env('FLAG_NEW_CHECKOUT_FLOW', false),
        'advanced_search' => env('FLAG_ADVANCED_SEARCH', false),
        'ai_recommendations' => env('FLAG_AI_RECOMMENDATIONS', false),
        'social_sharing' => env('FLAG_SOCIAL_SHARING', true),
        'product_reviews' => env('FLAG_PRODUCT_REVIEWS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Límites y Restricciones
    |--------------------------------------------------------------------------
    |
    | Configuración de límites del sistema según el plan o configuración
    |
    */
    'limits' => [
        'max_products' => env('LIMIT_MAX_PRODUCTS', 1000),
        'max_categories' => env('LIMIT_MAX_CATEGORIES', 100),
        'max_users' => env('LIMIT_MAX_USERS', 10000),
        'max_orders_per_day' => env('LIMIT_MAX_ORDERS_PER_DAY', 500),
        'max_file_upload_size' => env('LIMIT_MAX_FILE_UPLOAD_SIZE', 5), // MB
        'max_product_images' => env('LIMIT_MAX_PRODUCT_IMAGES', 10),
    ],
];
