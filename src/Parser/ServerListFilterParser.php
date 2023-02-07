<?php

namespace App\Parser;

class ServerListFilterParser
{
    // TODO get this options from the imported values instead to avoid options mismatch!
    public const STORAGE_OPTIONS = [0, 240, 500, 1024, 2048, 3072, 4096, 8192, 12288, 16384, 24576, 49152, 73728];
    public const STORAGE_TYPE_OPTIONS = ['sata2', 'ssd', 'sas'];
    public const RAM_OPTIONS = [2, 4, 8, 12, 16, 24, 32, 48, 64, 96];

    public function parse(array $filters): array
    {
        if (empty($filters['storage_type'])) {
            $filters['storage_type'] = self::STORAGE_TYPE_OPTIONS;
        } else {
            $this->parseStorageTypeFilter($filters);
        }

        if (empty($filters['ram']) || !is_array($filters['ram'])) {
            $filters['ram'] = self::RAM_OPTIONS;
        } else {
            $this->parseRamFilter($filters);
        }

        if (empty($filters['storage'])) {
            $filters['storage'] = self::STORAGE_OPTIONS;
        } else {
            $this->parseStorageFilter($filters);
        }

        return $filters;
    }

    private function parseStorageTypeFilter(array &$filters): void
    {
        $filters['storage_type'] = in_array($filters['storage_type'], self::STORAGE_TYPE_OPTIONS) ?
            [strtolower($filters['storage_type'])] :
            self::STORAGE_TYPE_OPTIONS;
    }

    private function parseRamFilter(array &$filters): void
    {
        $ramSelectedValues = reset($filters['ram']);
        $ramValues = [];
        if (!empty($ramSelectedValues)) {
            $ramSelectedValues = explode(',', $ramSelectedValues);
            foreach ($ramSelectedValues as $value) {
                if (!empty($value)) {
                    $ramValues[] = (int)$value;
                }
            }
            sort($ramValues);
        } else {
            $ramValues = self::RAM_OPTIONS;
        }
        $filters['ram'] = array_values(array_intersect(self::RAM_OPTIONS, $ramValues));
    }

    private function parseStorageFilter(array &$filters): void
    {
        $filters['storage'] = in_array($filters['storage'], self::STORAGE_OPTIONS) ?
            [strtolower($filters['storage'])] :
            self::STORAGE_OPTIONS;
    }
}
