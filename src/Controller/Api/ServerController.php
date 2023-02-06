<?php

namespace App\Controller\Api;

use App\Enums\DataRepositoryKeys;
use App\Repository\DataRepository;
use JetBrains\PhpStorm\ArrayShape;
use League\Flysystem\FilesystemException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerController extends AbstractController
{
    private readonly array $locations;

    /**
     * @param DataRepository $dataRepository
     * @throws FilesystemException
     * @throws InvalidArgumentException
     */
    public function __construct(private readonly DataRepository $dataRepository)
    {
        $this->locations = $this->dataRepository->getData(DataRepositoryKeys::LOCATIONS->key());
        $storageType = $this->dataRepository->getData(DataRepositoryKeys::STORAGE_TYPE_OPTIONS->key());
        $ramOptions = $this->dataRepository->getData(DataRepositoryKeys::RAM_OPTIONS->key());
        $storageOptions = $this->dataRepository->getData(DataRepositoryKeys::STORAGE_OPTIONS->key());
    }

    public function index(Request $request): JsonResponse
    {
        $location = $request->get('location', 'AmsterdamAMS-01');
        $location = empty($location) ? 'AmsterdamAMS-01' : $location;

        $serversList = [];

        try {
            $serversList = $this->dataRepository->getServersList($location, $request->query->all());

        } catch (FilesystemException|InvalidArgumentException $e) {

        }

        return $this->json($this->getData($serversList));
    }

    #[ArrayShape(['data' => "array"])]
    private function getData(array $serversList)
    {
        return ['data' => array_values($serversList['servers'])];
    }
}
