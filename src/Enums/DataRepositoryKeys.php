<?php

namespace App\Enums;
enum DataRepositoryKeys
{
    case SERVERS;
    case STORAGE_OPTIONS;
    case STORAGE_TYPE_OPTIONS;
    case RAM_OPTIONS;
    case LOCATIONS;

    public function key(): string
    {
        return match ($this) {
            DataRepositoryKeys::SERVERS => 'servers',
            DataRepositoryKeys::STORAGE_OPTIONS => 'storage_options',
            DataRepositoryKeys::STORAGE_TYPE_OPTIONS => 'storage_type_options',
            DataRepositoryKeys::RAM_OPTIONS => 'ram_options',
            DataRepositoryKeys::LOCATIONS => 'locations',
        };
    }
}
