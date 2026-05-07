<?php

namespace App\Enums;

enum ShopType: string
{
    case TECH_SHOP = 'tech_shop';

    /**
     * Get the display name for the shop type
     */
    public function label(): string
    {
        return match($this) {
            self::TECH_SHOP => 'Tech / Computer / Electronics',
        };
    }

    /**
     * Get the icon for the shop type
     */
    public function icon(): string
    {
        return match($this) {
            self::TECH_SHOP => 'device-desktop',
        };
    }

    /**
     * Get description for the shop type
     */
    public function description(): string
    {
        return match($this) {
            self::TECH_SHOP => 'Computer, tech, and electronics sales with repairs, warranty tracking, and serial numbers',
        };
    }

    /**
     * Get all shop types as array
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all shop types with labels
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($type) => [
            $type->value => $type->label()
        ])->toArray();
    }
}
