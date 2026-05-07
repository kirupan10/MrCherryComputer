<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PriceUpdateNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $productName,
        public readonly string $productCode,
        public readonly string $productSlug,
        public readonly string $updatedByName,
        public readonly ?float $oldSellingPrice,
        public readonly float  $newSellingPrice,
        public readonly ?float $oldBuyingPrice,
        public readonly ?float $newBuyingPrice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'              => 'price_update',
            'product_name'      => $this->productName,
            'product_code'      => $this->productCode,
            'product_slug'      => $this->productSlug,
            'updated_by'        => $this->updatedByName,
            'old_selling_price' => $this->oldSellingPrice,
            'new_selling_price' => $this->newSellingPrice,
            'old_buying_price'  => $this->oldBuyingPrice,
            'new_buying_price'  => $this->newBuyingPrice,
            'message'           => "{$this->updatedByName} updated the price of {$this->productName} ({$this->productCode})",
        ];
    }
}
