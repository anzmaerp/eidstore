<?php

namespace App\Services;

use Modules\TaxModule\app\Traits\VatTaxManagement;

class OrderDetailsService
{
    use VatTaxManagement;

    public function getPOSOrderDetailsData(int|string $orderId, array $item, object|array $product, float $price, float $tax): array
    {
        $taxConfig = self::getTaxSystemType();

        return [
            'order_id' => $orderId,
            'product_id' => $item['id'],
            'product_details' => $product,
            'qty' => $item['quantity'],
            'price' => $price,
            'seller_id' => $product['user_id'],
            'tax' => $tax,
            'tax_model' => $taxConfig['is_included'] ? 'include' : 'exclude',
            'discount' => $item['discount'] * $item['quantity'],
            'discount_type' => 'discount_on_product',
            'delivery_status' => 'delivered',
            'payment_status' => 'paid',
            'variant' => $item['variant'],
            'variation' => json_encode($item['variations']),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
