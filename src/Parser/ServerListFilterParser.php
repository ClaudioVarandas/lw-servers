<?php

namespace App\Parser;

class ServerListFilterParser
{
    // TODO get this options from the imported values instead to avoid options mismatch!
    public const STORAGE_OPTIONS = [0, 240, 500, 1024, 2048, 3072, 4096, 8192, 12288, 16384, 24576, 49152, 73728];
    public const STORAGE_TYPE_OPTIONS = ['sata2', 'ssd', 'sas'];
    public const RAM_OPTIONS = [2, 4, 8, 12, 16, 24, 32, 48, 64, 96];

    public const FILTERS_ALLOWED = ['storage_type', 'ram', 'storage'];

    public function parse(array $filters): array
    {
        $filters = array_intersect_key(
            $filters,
            array_flip(self::FILTERS_ALLOWED)
        );

        $filters['storage_type'] = empty($filters['storage_type']) ?
            self::STORAGE_TYPE_OPTIONS :
            $this->parseStorageTypeFilter($filters);

        $filters['ram'] = empty($filters['ram']) || !is_array($filters['ram']) ?
            self::RAM_OPTIONS :
            $this->parseRamFilter($filters);


        $filters['storage'] = empty($filters['storage']) ?
            self::STORAGE_OPTIONS :
            $this->parseStorageFilter($filters);

        return $filters;
    }

    private function parseStorageTypeFilter(array $filters): array
    {
        return in_array($filters['storage_type'], self::STORAGE_TYPE_OPTIONS) ?
            [strtolower($filters['storage_type'])] :
            self::STORAGE_TYPE_OPTIONS;
    }

    private function parseRamFilter(array $filters): array
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

        return array_values(array_intersect(self::RAM_OPTIONS, $ramValues));
    }

    private function parseStorageFilter(array $filters): array
    {
        return in_array($filters['storage'], self::STORAGE_OPTIONS) ?
            [strtolower($filters['storage'])] :
            self::STORAGE_OPTIONS;
    }
}
