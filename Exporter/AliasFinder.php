<?php

namespace SmMeleven\Exporter;

class AliasFinder
{
    /**
     * @var array<string, string>
     */
    private $cache = [];

    /**
     * @var array<array, boolean>
     */
    private $cacheIds = [];

    public function findAliasForBasename($basename)
    {
        if(isset($this->cache[$basename]) || array_key_exists($basename, $this->cache)) {
            return $this->cache[$basename];
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

        return $this->cacheIds[$melevenId] = null;
    }
}