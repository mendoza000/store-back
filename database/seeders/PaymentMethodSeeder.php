<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la store principal
        $store = Store::first();
        
        if (!$store) {
            $this->command->error('❌ No hay store disponible para crear métodos de pago');
            return;
        }

        $paymentMethods = [
            [
                'store_id' => $store->id,
                'name' => 'Pago Móvil Banco Provincial',
                'type' => 'mobile_payment',
                'account_info' => '0424-1234567',
                'instructions' => 'Enviar pago móvil a nombre de ZapatosExpress C.A., RIF J-12345678-9',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'Transferencia Bancaria - Banesco',
                'type' => 'bank_transfer',
                'account_info' => '0134-0123-45-1234567890',
                'instructions' => 'Cuenta corriente a nombre de ZapatosExpress C.A. - Por favor incluir número de pedido en la referencia',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'Transferencia Bancaria - Mercantil',
                'type' => 'bank_transfer',
                'account_info' => '0105-0987-65-9876543210',
                'instructions' => 'Cuenta de ahorros a nombre de ZapatosExpress - Enviar comprobante por WhatsApp',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'PayPal',
                'type' => 'paypal',
                'account_info' => 'pagos@zapatosexpress.com',
                'instructions' => 'Enviar pago a la cuenta de PayPal indicada. Aceptamos tarjetas de crédito/débito',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'Zelle',
                'type' => 'mobile_payment',
                'account_info' => 'zapatosexp@gmail.com',
                'instructions' => 'Enviar pago por Zelle al correo indicado. Incluir número de pedido en el memo',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'Binance Pay (USDT)',
                'type' => 'crypto',
                'account_info' => 'TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE',
                'instructions' => 'Enviar USDT (TRC20) a la dirección indicada. Enviar captura de transacción',
                'status' => 'active',
            ],
            [
                'store_id' => $store->id,
                'name' => 'Efectivo en Tienda',
                'type' => 'cash',
                'account_info' => 'Sucursal Centro Comercial Sambil',
                'instructions' => 'Pagar en efectivo al retirar en nuestra tienda física. Presentar número de pedido',
                'status' => 'active',
            ],
        ];

        foreach ($paymentMethods as $methodData) {
            PaymentMethod::create($methodData);
        }

        $this->command->info('✅ Métodos de pago creados exitosamente: ' . count($paymentMethods) . ' métodos para store: ' . $store->name);
    }
} 