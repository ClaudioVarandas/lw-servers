<?php

namespace App\Enums;

enum DataKeys
{
    case LOCATIONS;
    case STORAGE_OPTIONS;
    case STORAGE_TYPES_OPTIONS;
    case RAM_OPTIONS;
    case SERVERS;

    public function key(): string
    {
        return match ($this) {
            DataKeys::LOCATIONS => 'locations',
            DataKeys::STORAGE_OPTIONS => 'storage_options',
            DataKeys::STORAGE_TYPES_OPTIONS => 'storage_type_options',
            DataKeys::RAM_OPTIONS => 'ram_options',
            DataKeys::SERVERS => 'servers',
        };
    }
}
