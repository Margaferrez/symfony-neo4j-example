<?php
namespace Mentatik\GraphDB;

use GraphAware\Neo4j\OGM\EntityManager;

class GraphEntityManager
{
    private $em;

    public function __construct($host, $cacheDir = NULL)
    {
        $this->em = EntityManager::create($host, $cacheDir);
    }

    public function getEm()
    {
        return $this->em;
    }

}