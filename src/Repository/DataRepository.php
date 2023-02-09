<?php

namespace App\Repository;

use App\Enums\DataRepositoryKeys;
use App\Parser\ServerListFilterParser;
use Exception;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\TraceableAdapter;

class DataRepository
{
    public function __construct(
        private readonly TraceableAdapter $cache,
        private readonly FilesystemOperator $storage,
        private readonly ServerListFilterParser $serverListFilterParser
    ) {
    }

    /**
     * @throws FilesystemException|InvalidArgumentException
     */
    public function setData(string $key, array $data): void
    {
        $jsonEncoded = json_encode($data);

        $this->writeJsonToFile($key, $jsonEncoded);

        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit()) {
            $this->cache->deleteItem($key);
        }

        $cacheItem->set($jsonEncoded);
        $this->cache->save($cacheItem);
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception|FilesystemException
     */
    public function getData(string $key): array
    {
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            return json_decode($cacheItem->get(), true);
        }

        $fileLocation = 'db/' . $key . '.json';
        if ($this->storage->has($fileLocation)) {
            $contents = $this->readJsonFromFile($fileLocation);
            return $contents ? json_decode($contents, true) : [];
        }

        throw new Exception("Unable to load $key data");
    }

    /**
     * @throws FilesystemException
     * @throws InvalidArgumentException
     */
    public function getServersList(string $location, array $filters): array
    {
        $key = DataRepositoryKeys::SERVERS->key() . '_' . $location;
        $data = $this->getData($key);
        $servers = [];
        $serversFiltered = [];

        $filters = $this->serverListFilterParser->parse($filters);

        foreach ($data as $item) {
            $serversFiltered[] = array_filter($item, function ($value) use ($filters) {
                return in_array(strtolower($value['_storage-type']), $filters['storage_type'])
                    && in_array($value['_ram'], $filters['ram'])
                    && in_array($value['_storage-size'], $filters['storage']);
            });
        }

        $servers['servers'] = reset($serversFiltered);

        return $servers;
    }

    /**
     * @throws FilesystemException
     */
    private function writeJsonToFile(string $key, string $jsonEncoded): void
    {
        // Create a stream
        $stream = fopen('php://temp', 'rw');
        fwrite($stream, $jsonEncoded);
        rewind($stream);
        // Write the stream to the filesystem
        $this->storage->writeStream('db/' . $key . '.json', $stream);
        fclose($stream);
    }

    /**
     * @throws FilesystemException
     */
    private function readJsonFromFile(string $fileLocation): string|bool
    {
        $stream = $this->storage->readStream($fileLocation);
        $contents = stream_get_contents($stream);
        fclose($stream);

        return $contents;
    }
}
