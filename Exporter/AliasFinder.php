<?php

namespace Shopware\SmMeleven\Exporter;

use Shopware\CustomModels\MelevenImageRepository;

class AliasFinder
{
    /**
     * @var MelevenImageRepository
     */
    private $repository;

    /**
     * @var array<string, string>
     */
    private $cache = [];

    /**
     * @var array<array, boolean>
     */
    private $cacheIds = [];

    public function __construct(MelevenImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findAliasForBasename($basename)
    {
        if(isset($this->cache[$basename]) || array_key_exists($basename, $this->cache)) {
            return $this->cache[$basename];
        }

        if($name = $this->repository->findMelevenIdByBasename($basename)) {
            // prevent memory leaks
            if(count($this->cache) > 100) {
                $this->cache = array_slice($this->cache, 50);
            }

            return $this->cache[$basename] = $name;
        }
        
        return null;        
    }

    /**
     * @param string $melevenId "00.00.00.foo.jpg"
     * @return boolean
     */
    public function hasMelevenId($melevenId)
    {
        if (isset($this->cacheIds[$melevenId]) || array_key_exists($melevenId, $this->cacheIds)) {
            return $this->cacheIds[$melevenId];
        }

        // prevent memory leaks
        if (count($this->cacheIds) > 100) {
            $this->cacheIds = array_slice($this->cacheIds, 50);
        }

        return $this->cacheIds[$melevenId] = $this->repository->hasMelevenId($melevenId);
    }
}