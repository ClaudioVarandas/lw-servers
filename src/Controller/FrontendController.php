<?php

namespace App\Controller;

use App\Enums\DataKeys;
use App\Enums\DataRepositoryKeys;
use App\Repository\DataRepository;
use Exception;
use League\Flysystem\FilesystemException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends AbstractController
{
    /**
     * @param DataRepository $dataRepository
     */
    public function __construct(private readonly DataRepository $dataRepository)
    {
    }

    public function index(Request $request): Response
    {
        $location = $request->get('location','AmsterdamAMS-01');

        try {
            $serversList = $this->dataRepository->getServersList($location,$request->query->all());
            $locations = $this->dataRepository->getData(DataRepositoryKeys::LOCATIONS->key());
            $storageType = $this->dataRepository->getData(DataRepositoryKeys::STORAGE_TYPE_OPTIONS->key());
            $ramOptions = $this->dataRepository->getData(DataRepositoryKeys::RAM_OPTIONS->key());
            $storageOptions = $this->dataRepository->getData(DataRepositoryKeys::STORAGE_OPTIONS->key());

            if($request->get('storage',false)){
                foreach ($storageOptions[DataKeys::STORAGE_OPTIONS->key()] as $key => $option){
                    if($option['value'] == $request->get('storage')){
                        $storageValue = $key;
                    }
                }
            }

        } catch (Exception|FilesystemException|InvalidArgumentException $e) {
            //
        }

        return $this->render('frontend/index.html.twig', [
            'location' => $location,
            'serversList' => $serversList[DataKeys::SERVERS->key()] ?? [],
            'locations' => $locations[DataKeys::LOCATIONS->key()] ?? [],
            'storageType' => $storageType[DataKeys::STORAGE_TYPES_OPTIONS->key()] ?? [],
            'ramOptions' => $ramOptions[DataKeys::RAM_OPTIONS->key()] ?? [],
            'storageOptions' => $storageOptions[DataKeys::STORAGE_OPTIONS->key()] ?? [],
            'ramChecked' => $request->get('ram',[0 =>""]),
            'storageValue' => $storageValue ?? 0
        ]);
    }
}
