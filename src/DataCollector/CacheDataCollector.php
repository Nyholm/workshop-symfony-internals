<?php

declare(strict_types=1);

namespace App\DataCollector;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheDataCollector implements CacheItemPoolInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $real;

    private $calls;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->real = $cache;
    }

    public function getCalls()
    {
        return $this->calls;
    }

    public function getItem($key)
    {
        $this->calls['getItem'][] = ['key'=>$key];
        return $this->real->getItem($key);
    }

    public function getItems(array $keys = array())
    {
        $this->calls['getItems'][] = ['key'=>$keys];
        return $this->real->getItems($keys);
    }

    public function hasItem($key)
    {
        $this->calls['hasItem'][] = ['key'=>$key];
        return $this->real->hasItem($key);
    }

    public function clear()
    {
        $this->calls['clear'][] = [];
        return $this->real->clear();
    }

    public function deleteItem($key)
    {
        $this->calls['deleteItem'][] = ['key'=>$key];
        return $this->real->deleteItem($key);
    }

    public function deleteItems(array $keys)
    {
        $this->calls['deleteItems'][] = ['key'=>$keys];
        return $this->real->deleteItems($keys);
    }

    public function save(CacheItemInterface $item)
    {
        $this->calls['save'][] = [];
        return $this->real->save($item);
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        $this->calls['saveDeferred'][] = [];
        return $this->real->saveDeferred($item);
    }

    public function commit()
    {
        $this->calls['commit'][] = [];
        return $this->real->commit();
    }
}
