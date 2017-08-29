<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use DateInterval;
use Slick\Cache\Exception\InvalidArgumentException;

/**
 * CacheSimpleStorage
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
*/
class CacheSimpleStorage implements CacheSimpleStorageInterface
{
    /**
     * @var CacheDriverInterface
     */
    private $driver;

    /**
     * @var CacheStorage
     */
    private $cacheStorage;

    /**
     * CacheSimpleStorage
     *
     * @param CacheDriverInterface $driver
     */
    public function __construct(CacheDriverInterface $driver)
    {
        $this->driver = $driver;
        $this->cacheStorage = new CacheStorage($driver);
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        CacheStorage::checkKey($key);

        /** @var CacheItem $item */
        $item = unserialize($this->driver->get($key));

        if (! $item->isHit()) {
            return $default;
        }

        return $item->get();
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|int|DateInterval $ttl Optional.
     *      The TTL value of this item. If no value is sent and
     *      the driver supports TTL then the library may set a default value
     *      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   If the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null)
    {
        CacheStorage::checkKey($key);
        $item = $this->cacheStorage->getItem($key);
        $item->set($value)->expiresAfter($ttl);
        return $this->cacheStorage->save($item);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   if the $key string is not a legal value.
     */
    public function delete($key)
    {
        CacheStorage::checkKey($key);
        return $this->driver->erase($key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear()
    {
        return $this->driver->flush();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or
     *      are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   If $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        $valid = is_array($keys) || $keys instanceof \Traversable;
        if (! $valid) {
            throw new InvalidArgumentException(
                "Cannot iterate over keys. To get multiple values you should pass a " .
                "list of all keys you want to retrieve."
            );
        }

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|DateInterval $ttl Optional. The TTL value of this item.
     *      If no value is sent and the driver supports TTL then the library may
     *      set a default value for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   If $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null)
    {
        $valid = is_array($values) || $values instanceof \Traversable;
        if (! $valid) {
            throw new InvalidArgumentException(
                "Cannot iterate over values. To set multiple values you need to ".
                "pass a list of key => value pairs with the values you want to cache."
            );
        }

        foreach ($values as $key => $value) {
            if (! $this->set($key, $value, $ttl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        $valid = is_array($keys) || $keys instanceof \Traversable;
        if (! $valid) {
            throw new InvalidArgumentException(
                "Cannot iterate over keys. To delete multiple values you should pass a " .
                "list of all keys you want to delete."
            );
        }

        foreach ($keys as $key) {
            if (! $this->delete($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   if the $key string is not a legal value.
     */
    public function has($key)
    {
        CacheStorage::checkKey($key);

        return $this->driver->has($key);
    }
}
