<?php

namespace App\Tests\Unit\Parser;

use App\Parser\ServerListFilterParser;
use PHPUnit\Framework\TestCase;

class ServerListFilterParserTest extends TestCase
{
    public function testItShouldValidateAndParseFiltersWhenStorageTypeHasValue(): void
    {
        $serverListFilterParser = new ServerListFilterParser();
        $filtersParsed = $serverListFilterParser->parse(['storage_type' => 'sata2']);

        $this->assertIsArray($filtersParsed);
        $this->assertCount(3, $filtersParsed);
        $this->assertArrayHasKey('storage_type', $filtersParsed);
        $this->assertArrayHasKey('ram', $filtersParsed);
        $this->assertArrayHasKey('storage', $filtersParsed);
        $this->assertCount(1, $filtersParsed['storage_type']);
        $this->assertEquals('sata2', reset($filtersParsed['storage_type']));
        $this->assertCount(10, $filtersParsed['ram']);
        $this->assertCount(13, $filtersParsed['storage']);
    }

    /**
     * @dataProvider filtersDataProvider
     * @param array $filters
     * @return void
     */
    public function testItShouldValidateAndParseFilters(array $filters): void
    {
        $serverListFilterParser = new ServerListFilterParser();
        $filtersParsed = $serverListFilterParser->parse($filters);

        $this->assertIsArray($filtersParsed);
        $this->assertCount(3, $filtersParsed);
        $this->assertArrayHasKey('storage_type', $filtersParsed);
        $this->assertArrayHasKey('ram', $filtersParsed);
        $this->assertArrayHasKey('storage', $filtersParsed);
        $this->assertCount(1, $filtersParsed['storage_type']);
        $this->assertEquals($filters['storage_type'], reset($filtersParsed['storage_type']));

        $count = !empty($filters['ram']) ? count($filters['ram']) : count(ServerListFilterParser::RAM_OPTIONS);
        $this->assertCount($count, $filtersParsed['ram']);
        $this->assertIsArray($filtersParsed['ram']);

        if(!empty($filters['ram'])){
            $this->assertEquals($filters['ram'], $filtersParsed['ram']);
        }

        if (!empty($filters['storage_type'])) {
            $this->assertEquals($filters['storage_type'], reset($filtersParsed['storage_type']));
            $this->assertCount(1, $filtersParsed['storage_type']);
        }else{
            $this->assertCount(count(ServerListFilterParser::STORAGE_TYPE_OPTIONS), $filtersParsed['storage_type']);
        }

        if (!empty($filters['storage_type'])) {
            $this->assertEquals($filters['storage_type'], reset($filtersParsed['storage_type']));
            $this->assertCount(1, $filtersParsed['storage_type']);
        }else{
            $this->assertCount(count(ServerListFilterParser::STORAGE_TYPE_OPTIONS), $filtersParsed['storage_type']);
        }

    }

    /**
     * @return array[]
     */
    private function filtersDataProvider(): array
    {
        return [
            'scenario_a' => [
                'filters' => [
                    'storage_type' => 'sata2',
                    'ram' => [2, 4, 16],
                    'storage' => 2048
                ]
            ],
            'scenario_b' => [
                'filters' => [
                    'storage_type' => 'ssd',
                    'ram' => [2],
                    'storage' => 2048
                ]
            ],
            'scenario_c' => [
                'filters' => [
                    'storage_type' => 'ssd',
                    'ram' => [2]
                ]
            ],
            'scenario_d' => [
                'filters' => [
                    'storage_type' => 'ssd',
                    'storage' => 2048
                ]
            ],
            //...
        ];
    }
}
