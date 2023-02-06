<?php

namespace App\Tests\Functional\Repository;

use App\Enums\DataKeys;
use App\Enums\DataRepositoryKeys;
use App\Repository\DataRepository;
use Monolog\Handler\TestHandler;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataRepositoryTest extends KernelTestCase
{
    public function testShouldWriteAndReadDataFromDataRepository(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        /** @var DataRepository $dataRepository */
        $dataRepository = static::getContainer()->get(DataRepository::class);

        $dataRepository->setData(
            DataRepositoryKeys::LOCATIONS->key(),
            [DataKeys::LOCATIONS->key() => $this->getLocationsData()]
        );

        $data = $dataRepository->getData(DataRepositoryKeys::LOCATIONS->key());

        $this->assertIsArray($data);
        $this->assertArrayHasKey('locations',$data);
        foreach ($data['locations'] as $item){
            $this->assertArrayHasKey('value',$item);
            $this->assertArrayHasKey('label',$item);
        }
    }

    private function getLocationsData()
    {
        $data = '[{"value":"AmsterdamAMS-01","label":"AmsterdamAMS-01"},{"value":"Washington-D-C-WDC-01","label":"Washington D.C.WDC-01"},{"value":"San-FranciscoSFO-12","label":"San FranciscoSFO-12"},{"value":"SingaporeSIN-11","label":"SingaporeSIN-11"},{"value":"DallasDAL-10","label":"DallasDAL-10"},{"value":"FrankfurtFRA-10","label":"FrankfurtFRA-10"},{"value":"Hong-KongHKG-10","label":"Hong KongHKG-10"}]';

        return json_decode($data,true);
    }
}
