<?php

namespace App\Service;

use App\Enums\DataKeys;
use App\Enums\DataRepositoryKeys;
use App\Repository\DataRepository;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Reader\XLSX\Reader;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class ServerListFileProcessor
{
    private const HEADERS = [
        'model',
        'ram',
        'hdd',
        'location',
        'price'
    ];

    private const TERABYTE_ACRONYM = 'TB';
    private const STORAGE_TYPES = ['SATA2', 'SSD', 'SAS'];
    private const STORAGE_UNIT = [self::TERABYTE_ACRONYM, 'GB',];
    private const STORAGE_OPTIONS = [
        ['label' => '0', 'value' => 0],
        ['label' => '240GB', 'value' => 240],
        ['label' => '500GB', 'value' => 500],
        ['label' => '1TB', 'value' => 1024],
        ['label' => '2TB', 'value' => 2048],
        ['label' => '3TB', 'value' => 3072],
        ['label' => '4TB', 'value' => 4096],
        ['label' => '8TB', 'value' => 8192],
        ['label' => '12TB', 'value' => 12288],
        ['label' => '24TB', 'value' => 24576],
        ['label' => '48TB', 'value' => 49152],
        ['label' => '72TB', 'value' => 73728],
    ];

    private const STORAGE_TYPE_OPTIONS = [
        ['label' => 'SaS', 'value' => 'sas'],
        ['label' => 'Sata2', 'value' => 'sata2'],
        ['label' => 'SSD', 'value' => 'ssd'],
    ];
    private const RAM_OPTIONS = [
        ['label' => '2GB', 'value' => 2],
        ['label' => '4GB', 'value' => 4],
        ['label' => '8GB', 'value' => 8],
        ['label' => '12GB', 'value' => 12],
        ['label' => '16GB', 'value' => 16],
        ['label' => '24GB', 'value' => 24],
        ['label' => '32GB', 'value' => 32],
        ['label' => '48GB', 'value' => 48],
        ['label' => '64GB', 'value' => 64],
        ['label' => '96GB', 'value' => 96],
    ];

    private Reader $reader;

    private array $data;
    private array $locations;

    public function __construct(
        private readonly FilesystemOperator $storage,
        private readonly DataRepository $dataRepository,
        private readonly SluggerInterface $slugger
    ) {
        $this->reader = new Reader();
        $this->locations = [];
        $this->data = [];
    }

    /**
     * @param File $file
     * @return void
     * @throws IOException
     * @throws ReaderNotOpenedException|FilesystemException|InvalidArgumentException
     */
    public function process(File $file): void
    {
        $filePath = sprintf('%s/%s', $file->getPath(), $file->getFilename());

        $this->reader->open($filePath);

        foreach ($this->reader->getSheetIterator() as $sheet) {
            if ($sheet->getName() === 'Sheet2') {
                foreach ($sheet->getRowIterator() as $key => $row) {
                    // Assuming the row 1 is the header
                    if ($key === 1) {
                        continue;
                    }

                    $rowData = [];
                    $locationSlug = '';

                    foreach ($row->getCells() as $cKey => $cell) {
                        if ($cKey < count(self::HEADERS)) {
                            $header = self::HEADERS[$cKey];

                            $cellValue = $cell->getValue();
                            $rowData[$header] = $cellValue;

                            if ($header === 'hdd') {
                                $this->handleStorage($rowData, $cellValue);
                            }

                            if ($header === 'ram') {
                                $this->handleRam($rowData, $cellValue);
                            }

                            if ($header === 'location') {
                                $locationSlug = $this->slugger->slug($cellValue)->toString();
                                $this->storeLocation($locationSlug, $cellValue);
                            }
                        }
                    }

                    $this->data[$locationSlug][] = $rowData;
                }
                break; // no need to read more sheets
            }
        }

        $this->reader->close();
        //
        $this->storeData();
        //
        $this->storage->delete('uploads/' . $file->getFilename());
    }

    private function handleStorage(&$rowData, string $cellValue): void
    {
        foreach (self::STORAGE_UNIT as $unit) {
            $unitTypeArray = [];
            if (str_contains($cellValue, $unit)) {
                $unitTypeArray[] = $unit;
                $rowData['_storage-unit'] = $unit;
                foreach (self::STORAGE_TYPES as $type) {
                    if (str_contains($cellValue, $type)) {
                        $unitTypeArray[] = $type;
                        $rowData['_storage-type'] = $type;
                    }
                }

                $hddArray = explode(implode($unitTypeArray), $cellValue);
                $hddSizeValues = explode('x', $hddArray[0]);

                $product = array_product($hddSizeValues);
                $hddRealSize = ($unit === self::TERABYTE_ACRONYM) ? $product * 1024 : $product;
                $rowData['_storage-size'] = $hddRealSize;
            }
        }
    }

    private function handleRam(&$rowData, string $cellValue): void
    {
        $ramArray = explode('GBDDR', $cellValue);
        $rowData['_ram'] = $ramArray[0] ?? null;
    }

    private function storeLocation(string $locationSlug, string $cellValue): void
    {
        if (!$this->locationExists($cellValue)) {
            $this->locations[] = [
                'value' => $locationSlug,
                'label' => $cellValue
            ];
        }
    }

    /**
     * Function to check if the location already exists on the locations list.
     *
     * @param string $value
     * @return bool
     */
    private function locationExists(string $value): bool
    {
        foreach ($this->locations as $item) {
            if ($item['label'] === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     * @throws FilesystemException
     */
    private function storeData(): void
    {
        foreach ($this->data as $locationSlug => $list) {
            $key = DataRepositoryKeys::SERVERS->key() . '_' . $locationSlug;
            $this->dataRepository->setData($key, [DataKeys::SERVERS->key() => $list]);
        }
        $this->dataRepository->setData(
            DataRepositoryKeys::STORAGE_OPTIONS->key(),
            [DataKeys::STORAGE_OPTIONS->key() => self::STORAGE_OPTIONS]
        );
        $this->dataRepository->setData(
            DataRepositoryKeys::STORAGE_TYPE_OPTIONS->key(),
            [DataKeys::STORAGE_TYPES_OPTIONS->key() => self::STORAGE_TYPE_OPTIONS]
        );
        $this->dataRepository->setData(
            DataRepositoryKeys::RAM_OPTIONS->key(),
            [DataKeys::RAM_OPTIONS->key() => self::RAM_OPTIONS]
        );
        $this->dataRepository->setData(
            DataRepositoryKeys::LOCATIONS->key(),
            [DataKeys::LOCATIONS->key() => $this->locations]
        );
    }
}
