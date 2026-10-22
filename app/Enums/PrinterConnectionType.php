<?php

namespace App\Enums;

enum PrinterConnectionType: string
{
    case Network = 'network';
    case Bluetooth = 'bluetooth';
    case Usb = 'usb';

    public function label(): string
    {
        return match ($this) {
            self::Network => 'Network / WiFi',
            self::Bluetooth => 'Bluetooth',
            self::Usb => 'USB',
        };
    }
}
