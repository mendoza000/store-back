<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateClientConfig extends Command
{
    protected $signature = 'store:configure 
                           {template=basic : Template to use (basic, default, premium)}
                           {--force : Overwrite existing configuration}';

    protected $description = 'Configure store modules and features using predefined templates';

    public function handle(): int
    {
        $template = $this->argument('template');
        $force = $this->option('force');

        // Verificar si ya existe configuración
        $configFile = config_path('modules.php');
        if (File::exists($configFile) && !$force) {
            $overwrite = $this->confirm("Configuration file already exists. Do you want to overwrite it?");
            if (!$overwrite) {
                $this->info('Configuration generation cancelled.');
                return self::SUCCESS;
            }
        }

        // Generar configuración según template
        try {
            $configContent = $this->generateConfigurationContent($template);
            File::put($configFile, $configContent);

            $this->info("✅ Store configuration generated successfully!");
            $this->info("📁 Configuration file: {$configFile}");
            $this->newLine();

            // Mostrar configuración de ejemplo para .env
            $this->displayEnvVariables($template);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error generating configuration: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function generateConfigurationContent(string $template): string
    {
        $templates = [
            'basic' => $this->getBasicTemplate(),
            'default' => $this->getDefaultTemplate(),
            'premium' => $this->getPremiumTemplate(),
        ];

        if (!isset($templates[$template])) {
            throw new \InvalidArgumentException("Unknown template: {$template}. Available: " . implode(', ', array_keys($templates)));
        }

        return $templates[$template];
    }

    private function getBasicTemplate(): string
    {
        return <<<'PHP'
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Módulos de la Tienda - Template Básico
    |--------------------------------------------------------------------------
    |
    | Configuración mínima para una tienda básica
    |
    */

    'store' => [
        'name' => env('STORE_NAME', 'Mi Tienda Básica'),
        'description' => env('STORE_DESCRIPTION', 'Una tienda en línea simple y efectiva'),
        'currency' => env('STORE_CURRENCY', 'USD'),
        'timezone' => env('STORE_TIMEZONE', 'America/Caracas'),
        'language' => env('STORE_LANGUAGE', 'es'),
    ],

    'modules' => [
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

        'commerce' => [
            'enabled' => true,
            'features' => [
                'cart' => true,
                'checkout' => true,
                'product_variants' => false,
                'inventory_tracking' => true,
                'price_comparison' => false,
                'bulk_pricing' => false,
            ],
            'settings' => [
                'max_cart_items' => 20,
                'cart_session_minutes' => 1440,
                'auto_reserve_stock' => false,
                'stock_reservation_minutes' => 15,
            ]
        ],

        'payments' => [
            'enabled' => true,
            'features' => [
                'manual_payments' => true,
                'bank_transfers' => true,
                'mobile_payments' => false,
                'cash_payments' => false,
                'crypto_payments' => false,
            ],
            'settings' => [
                'payment_verification_required' => true,
                'auto_approve_payments' => false,
                'payment_timeout_hours' => 48,
            ]
        ],

        'marketing' => [
            'enabled' => false,
            'features' => [
                'coupons' => false,
                'discounts' => false,
                'promotions' => false,
                'loyalty_program' => false,
                'referral_system' => false,
            ],
            'settings' => [
                'max_coupon_uses' => 100,
                'coupon_stacking' => false,
                'auto_apply_best_discount' => false,
            ]
        ],

        'inventory' => [
            'enabled' => true,
            'features' => [
                'stock_management' => true,
                'low_stock_alerts' => false,
                'stock_history' => false,
                'advanced_inventory' => false,
                'multi_warehouse' => false,
            ],
            'settings' => [
                'low_stock_threshold' => 5,
                'auto_disable_out_of_stock' => true,
                'backorder_allowed' => false,
            ]
        ],

        'users' => [
            'enabled' => true,
            'features' => [
                'customer_registration' => true,
                'user_profiles' => true,
                'wishlist' => false,
                'order_history' => true,
                'user_reviews' => false,
                'social_login' => false,
            ],
            'settings' => [
                'require_email_verification' => false,
                'allow_guest_checkout' => true,
                'min_password_length' => 6,
            ]
        ],

        'notifications' => [
            'enabled' => true,
            'features' => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'whatsapp_notifications' => false,
                'push_notifications' => false,
            ],
            'settings' => [
                'order_status_emails' => true,
                'payment_confirmation_emails' => true,
                'low_stock_admin_emails' => false,
            ]
        ],

        'administration' => [
            'enabled' => true,
            'features' => [
                'admin_dashboard' => true,
                'user_management' => true,
                'order_management' => true,
                'product_management' => true,
                'reports' => false,
                'advanced_analytics' => false,
                'export_data' => false,
            ],
            'settings' => [
                'admin_session_timeout' => 120,
                'require_admin_2fa' => false,
                'audit_trail' => false,
            ]
        ],
    ],

    'dependencies' => [
        'commerce' => ['core'],
        'payments' => ['core', 'commerce'],
        'marketing' => ['core', 'commerce'],
        'inventory' => ['core', 'commerce'],
        'notifications' => ['core'],
        'administration' => ['core'],
    ],

    'feature_flags' => [
        'new_checkout_flow' => false,
        'advanced_search' => false,
        'ai_recommendations' => false,
        'social_sharing' => false,
        'product_reviews' => false,
    ],

    'limits' => [
        'max_products' => 100,
        'max_categories' => 20,
        'max_users' => 500,
        'max_orders_per_day' => 50,
        'max_file_upload_size' => 2,
        'max_product_images' => 5,
    ],
];
PHP;
    }

    private function getDefaultTemplate(): string
    {
        return <<<'PHP'
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Módulos de la Tienda - Template Default
    |--------------------------------------------------------------------------
    |
    | Configuración balanceada para una tienda estándar
    |
    */

    'store' => [
        'name' => env('STORE_NAME', 'Mi Tienda Online'),
        'description' => env('STORE_DESCRIPTION', 'Tienda en línea completa con todas las características esenciales'),
        'currency' => env('STORE_CURRENCY', 'USD'),
        'timezone' => env('STORE_TIMEZONE', 'America/Caracas'),
        'language' => env('STORE_LANGUAGE', 'es'),
    ],

    'modules' => [
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

        'commerce' => [
            'enabled' => true,
            'features' => [
                'cart' => true,
                'checkout' => true,
                'product_variants' => true,
                'inventory_tracking' => true,
                'price_comparison' => true,
                'bulk_pricing' => false,
            ],
            'settings' => [
                'max_cart_items' => 50,
                'cart_session_minutes' => 1440,
                'auto_reserve_stock' => true,
                'stock_reservation_minutes' => 15,
            ]
        ],

        'payments' => [
            'enabled' => true,
            'features' => [
                'manual_payments' => true,
                'bank_transfers' => true,
                'mobile_payments' => true,
                'cash_payments' => false,
                'crypto_payments' => false,
            ],
            'settings' => [
                'payment_verification_required' => true,
                'auto_approve_payments' => false,
                'payment_timeout_hours' => 48,
            ]
        ],

        'marketing' => [
            'enabled' => true,
            'features' => [
                'coupons' => true,
                'discounts' => true,
                'promotions' => false,
                'loyalty_program' => false,
                'referral_system' => false,
            ],
            'settings' => [
                'max_coupon_uses' => 1000,
                'coupon_stacking' => false,
                'auto_apply_best_discount' => true,
            ]
        ],

        'inventory' => [
            'enabled' => true,
            'features' => [
                'stock_management' => true,
                'low_stock_alerts' => true,
                'stock_history' => false,
                'advanced_inventory' => false,
                'multi_warehouse' => false,
            ],
            'settings' => [
                'low_stock_threshold' => 5,
                'auto_disable_out_of_stock' => true,
                'backorder_allowed' => false,
            ]
        ],

        'users' => [
            'enabled' => true,
            'features' => [
                'customer_registration' => true,
                'user_profiles' => true,
                'wishlist' => true,
                'order_history' => true,
                'user_reviews' => false,
                'social_login' => false,
            ],
            'settings' => [
                'require_email_verification' => false,
                'allow_guest_checkout' => true,
                'min_password_length' => 8,
            ]
        ],

        'notifications' => [
            'enabled' => true,
            'features' => [
                'email_notifications' => true,
                'sms_notifications' => false,
                'whatsapp_notifications' => false,
                'push_notifications' => false,
            ],
            'settings' => [
                'order_status_emails' => true,
                'payment_confirmation_emails' => true,
                'low_stock_admin_emails' => true,
            ]
        ],

        'administration' => [
            'enabled' => true,
            'features' => [
                'admin_dashboard' => true,
                'user_management' => true,
                'order_management' => true,
                'product_management' => true,
                'reports' => true,
                'advanced_analytics' => false,
                'export_data' => true,
            ],
            'settings' => [
                'admin_session_timeout' => 120,
                'require_admin_2fa' => false,
                'audit_trail' => true,
            ]
        ],
    ],

    'dependencies' => [
        'commerce' => ['core'],
        'payments' => ['core', 'commerce'],
        'marketing' => ['core', 'commerce'],
        'inventory' => ['core', 'commerce'],
        'notifications' => ['core'],
        'administration' => ['core'],
    ],

    'feature_flags' => [
        'new_checkout_flow' => false,
        'advanced_search' => false,
        'ai_recommendations' => false,
        'social_sharing' => true,
        'product_reviews' => false,
    ],

    'limits' => [
        'max_products' => 1000,
        'max_categories' => 100,
        'max_users' => 10000,
        'max_orders_per_day' => 500,
        'max_file_upload_size' => 5,
        'max_product_images' => 10,
    ],
];
PHP;
    }

    private function getPremiumTemplate(): string
    {
        return <<<'PHP'
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Módulos de la Tienda - Template Premium
    |--------------------------------------------------------------------------
    |
    | Configuración completa para una tienda premium con todas las características
    |
    */

    'store' => [
        'name' => env('STORE_NAME', 'Mi Tienda Premium'),
        'description' => env('STORE_DESCRIPTION', 'Tienda en línea premium con todas las características avanzadas'),
        'currency' => env('STORE_CURRENCY', 'USD'),
        'timezone' => env('STORE_TIMEZONE', 'America/Caracas'),
        'language' => env('STORE_LANGUAGE', 'es'),
    ],

    'modules' => [
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

        'commerce' => [
            'enabled' => true,
            'features' => [
                'cart' => true,
                'checkout' => true,
                'product_variants' => true,
                'inventory_tracking' => true,
                'price_comparison' => true,
                'bulk_pricing' => true,
            ],
            'settings' => [
                'max_cart_items' => 100,
                'cart_session_minutes' => 2880,
                'auto_reserve_stock' => true,
                'stock_reservation_minutes' => 30,
            ]
        ],

        'payments' => [
            'enabled' => true,
            'features' => [
                'manual_payments' => true,
                'bank_transfers' => true,
                'mobile_payments' => true,
                'cash_payments' => true,
                'crypto_payments' => true,
            ],
            'settings' => [
                'payment_verification_required' => true,
                'auto_approve_payments' => false,
                'payment_timeout_hours' => 72,
            ]
        ],

        'marketing' => [
            'enabled' => true,
            'features' => [
                'coupons' => true,
                'discounts' => true,
                'promotions' => true,
                'loyalty_program' => true,
                'referral_system' => true,
            ],
            'settings' => [
                'max_coupon_uses' => 10000,
                'coupon_stacking' => true,
                'auto_apply_best_discount' => true,
            ]
        ],

        'inventory' => [
            'enabled' => true,
            'features' => [
                'stock_management' => true,
                'low_stock_alerts' => true,
                'stock_history' => true,
                'advanced_inventory' => true,
                'multi_warehouse' => true,
            ],
            'settings' => [
                'low_stock_threshold' => 10,
                'auto_disable_out_of_stock' => false,
                'backorder_allowed' => true,
            ]
        ],

        'users' => [
            'enabled' => true,
            'features' => [
                'customer_registration' => true,
                'user_profiles' => true,
                'wishlist' => true,
                'order_history' => true,
                'user_reviews' => true,
                'social_login' => true,
            ],
            'settings' => [
                'require_email_verification' => true,
                'allow_guest_checkout' => true,
                'min_password_length' => 8,
            ]
        ],

        'notifications' => [
            'enabled' => true,
            'features' => [
                'email_notifications' => true,
                'sms_notifications' => true,
                'whatsapp_notifications' => true,
                'push_notifications' => true,
            ],
            'settings' => [
                'order_status_emails' => true,
                'payment_confirmation_emails' => true,
                'low_stock_admin_emails' => true,
            ]
        ],

        'administration' => [
            'enabled' => true,
            'features' => [
                'admin_dashboard' => true,
                'user_management' => true,
                'order_management' => true,
                'product_management' => true,
                'reports' => true,
                'advanced_analytics' => true,
                'export_data' => true,
            ],
            'settings' => [
                'admin_session_timeout' => 240,
                'require_admin_2fa' => true,
                'audit_trail' => true,
            ]
        ],
    ],

    'dependencies' => [
        'commerce' => ['core'],
        'payments' => ['core', 'commerce'],
        'marketing' => ['core', 'commerce'],
        'inventory' => ['core', 'commerce'],
        'notifications' => ['core'],
        'administration' => ['core'],
    ],

    'feature_flags' => [
        'new_checkout_flow' => true,
        'advanced_search' => true,
        'ai_recommendations' => true,
        'social_sharing' => true,
        'product_reviews' => true,
    ],

    'limits' => [
        'max_products' => 50000,
        'max_categories' => 1000,
        'max_users' => 100000,
        'max_orders_per_day' => 5000,
        'max_file_upload_size' => 20,
        'max_product_images' => 50,
    ],
];
PHP;
    }

    private function displayEnvVariables(string $template): void
    {
        $this->info("🔧 Environment Variables Configuration:");
        $this->line("Add these variables to your .env file:");
        $this->newLine();

        $envVars = [
            'basic' => [
                'STORE_NAME="Mi Tienda Básica"',
                'STORE_DESCRIPTION="Una tienda en línea simple y efectiva"',
                'STORE_CURRENCY="USD"',
                'STORE_TIMEZONE="America/Caracas"',
                'STORE_LANGUAGE="es"',
            ],
            'default' => [
                'STORE_NAME="Mi Tienda Online"',
                'STORE_DESCRIPTION="Tienda en línea completa con todas las características esenciales"',
                'STORE_CURRENCY="USD"',
                'STORE_TIMEZONE="America/Caracas"',
                'STORE_LANGUAGE="es"',
            ],
            'premium' => [
                'STORE_NAME="Mi Tienda Premium"',
                'STORE_DESCRIPTION="Tienda en línea premium con todas las características avanzadas"',
                'STORE_CURRENCY="USD"',
                'STORE_TIMEZONE="America/Caracas"',
                'STORE_LANGUAGE="es"',
            ],
        ];

        foreach ($envVars[$template] as $var) {
            $this->line("  {$var}");
        }

        $this->newLine();
        $this->info("💡 Tips:");
        $this->line("  • Clear cache after making changes: php artisan config:clear");
        $this->line("  • You can modify any configuration in config/modules.php");
        $this->line("  • Override any setting using environment variables");
        $this->newLine();

        $this->info("📚 Usage Examples:");
        $this->line("  • Check if module is enabled: ModuleService::isModuleEnabled('commerce')");
        $this->line("  • Check if feature is enabled: ModuleService::isFeatureEnabled('commerce', 'cart')");
        $this->line("  • Get store config: ModuleService::getStoreConfig()");
    }
}
