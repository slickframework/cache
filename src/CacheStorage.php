<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use Slick\Cache\Driver\CacheDriverInterface;

/**
 * Cache Storage
 *
 * @package Slick\Cache
 */
class CacheStorage implements CacheStorageInterface
{

    /**
     * @var CacheDriverInterface
     */
    protected $driver;

    /**
     * Cache Storage created with cache driver dependency
     *
     * @param CacheDriverInterface $driver
     */
    public function __construct(CacheDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Retrieves a previously stored value.
     *
     * @param String $key The key under witch value was stored.
     * @param mixed $default The default value, if no value was stored before.
     *
     * @return mixed
     *  The stored value or the default value if it was not found
     *  on service cache.
     */
    public function get($key, $default = false)
    {
        $item = $this->driver->get($key);
        if ($item->isValid()) {
            $default = $item->getData();
        }
        return $default;
    }

    /**
     * Set/stores a value with a given key.
     *
     * @param String $key The key where value will be stored.
     * @param mixed $value The value to store.
     * @param integer $duration The live time of cache in seconds.
     *
     * @return self|$this A self instance for chaining method calls.
     */
    public function set($key, $value, $duration = self::CACHE_DEFAULT)
    {
        $item = $this->driver->getCacheItem()
            ->setKey($key)
            ->set($value, $duration);
        $this->driver->set($item);
        return $this;
    }

    /**
     * Erase the value stored with a given key.
     *
     * You can use the "?" and "*" wildcards to delete all matching keys.
     * The "?" means a place holders for one unknown character, the "*" is
     * a place holder for various characters.
     *
     * @param String $key The key under witch value was stored.
     *
     * @return self A self instance for chaining method calls.
     */
    public function erase($key)
    {
        $this->driver->erase($key);
        return $this;
    }

    /**
     * Flushes all values controlled by this cache driver
     *
     * @return self A self instance for chaining method calls.
     */
    public function flush()
    {
        $this->driver->flush();
        return $this;
    }
}