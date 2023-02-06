<?php

namespace App\Parser;

class ServerListFilterParser
{
    // TODO get this options from the imported values instead to avoid options mismatch!
    private const STORAGE_OPTIONS = [0, 240, 500, 1024, 2048, 3072, 4096, 8192, 12288, 16384,24576, 49152, 73728];
    private const STORAGE_TYPE_OPTIONS = ['sata2', 'ssd', 'sas'];
    private const RAM_OPTIONS = [2, 4, 8, 12, 16, 24, 32, 48, 64, 96];

    public function parse(array $filters): array
    {
        if (empty($filters['storage_type'])) {
            $filters['storage_type'] = self::STORAGE_TYPE_OPTIONS;
        } else {
            $filters['storage_type'] = in_array($filters['storage_type'], self::STORAGE_TYPE_OPTIONS) ?
                [strtolower($filters['storage_type'])] :
                self::STORAGE_TYPE_OPTIONS;
        }

        if (empty($filters['ram']) || !is_array($filters['ram'])) {
            $filters['ram'] = self::RAM_OPTIONS;
        } else {
            $filters['ram'] = array_values(array_intersect(self::RAM_OPTIONS, $filters['ram']));
        }

        if (empty($filters['storage'])) {
            $filters['storage'] = self::STORAGE_OPTIONS;
        } else {
            $filters['storage'] = in_array($filters['storage'], self::STORAGE_OPTIONS) ?
                [strtolower($filters['storage'])] :
                self::STORAGE_OPTIONS;
        }

        return $filters;
    }
}
