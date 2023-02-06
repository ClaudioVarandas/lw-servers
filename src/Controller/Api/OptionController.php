<?php

namespace App\Controller\Api;

use App\Enums\DataKeys;
use App\Enums\DataRepositoryKeys;
use App\Repository\DataRepository;
use Exception;
use League\Flysystem\FilesystemException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptionController extends AbstractController
{
    /**
     * @param DataRepository $dataRepository
     */
    public function __construct(private readonly DataRepository $dataRepository)
    {
    }

    public function getLocations(): JsonResponse
    {
        $data = [];

        try {
            $data = $this->dataRepository->getData(
                DataRepositoryKeys::LOCATIONS->key()
            );

        } catch (Exception|FilesystemException|InvalidArgumentException $e) {

        }

        return $this->json($this->getData($data, DataKeys::LOCATIONS->key()));
    }

    public function getStorageTypes(): JsonResponse
    {
        $data = [];

        try {
            $data = $this->dataRepository->getData(
                DataRepositoryKeys::STORAGE_TYPE_OPTIONS->key()
            );

        } catch (Exception|FilesystemException|InvalidArgumentException $e) {

        }
        return $this->json($this->getData($data, DataKeys::STORAGE_TYPES_OPTIONS->key()));
    }

    public function getStorage(): JsonResponse
    {
        $data = [];

        try {
            $data = $this->dataRepository->getData(
                DataRepositoryKeys::STORAGE_OPTIONS->key()
            );

        } catch (Exception|FilesystemException|InvalidArgumentException $e) {

        }

        return $this->json($this->getData($data, DataKeys::STORAGE_OPTIONS->key()));
    }

    public function getRam(): JsonResponse
    {
        $data = [];

        try {
            $data = $this->dataRepository->getData(
                DataRepositoryKeys::RAM_OPTIONS->key()
            );

        } catch (Exception|FilesystemException|InvalidArgumentException $e) {

        }

        return $this->json($this->getData($data, DataKeys::RAM_OPTIONS->key()));
    }

    private function getData(array $data, string $key): array
    {
        return ['data' => array_values($data[$key] ?? [])];
    }
}
