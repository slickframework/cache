<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use Psr\Cache\CacheItemInterface;
use Slick\Cache\Exception\InvalidArgumentException;

/**
 * CacheStorage
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
*/
class CacheStorage implements CacheStorageInterface
{
    /**
     * @var CacheDriverInterface
     */
    private $driver;

    /**
     * @var CacheItem[]
     */
    private $items = [];

    /**
     * @var CacheItem[]
     */
    private $deferred = [];

    /**
     * @var bool
     */
    private $commitState = true;

    /**
     * Creates a Cache Storage (pool)
     *
     * @param CacheDriverInterface $driver
     */
    public function __construct(CacheDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method will always return a CacheItemInterface object, even in case of
     * a cache miss.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        $this->checkKey($key);

        if (! $this->hasItem($key)) {
            return new CacheItem($key);
        }

        $currentPool = $this->currentPoolCollection();

        if (array_key_exists($key, $currentPool)) {
            return $currentPool[$key];
        }

        return unserialize($this->driver->get($key));
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = array())
    {
        $items = [];
        foreach ($keys as $key) {
            if ($this->hasItem($key)) {
                $items[$key] = $this->getItem($key);
            }
        }
        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        $this->checkKey($key);

        $inCurrentPool = array_key_exists($key, $this->currentPoolCollection());

        return $inCurrentPool
            ? $inCurrentPool
            : $this->driver->has($key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->items = [];
        return $this->driver->flush();
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        $this->checkKey($key);

        if (! $this->hasItem($key)) {
            return true;
        }

        unset($this->items[$key]);

        return $this->driver->erase($key);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.

     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            if (! $this->deleteItem($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        if (! $item instanceof \Slick\Cache\CacheItemInterface) {
            return false;
        }

        $this->checkKey($item->getKey());

        $this->items[$item->getKey()] = $item;

        return $this->driver->set(
            $item->getKey(),
            serialize($item),
            $item->expires()
        );
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted
     *   and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if (! $this->commitState) {
            return false;
        }

        $this->checkKey($item->getKey());

        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there
     *   were none. False otherwise.
     */
    public function commit()
    {
        while (! empty($this->deferred)) {
            $item = array_pop($this->deferred);
            if (! $this->save($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if provided key is valid according to PSR-6
     *
     * @see http://www.php-fig.org/psr/psr-6/#definitions
     *
     * @param string $key
     */
    private function checkKey(string $key)
    {
        $exp = '/^[0-9a-z\._]{1,256}$/i';
        if (! preg_match($exp, $key)) {
            throw new InvalidArgumentException(
                "Invalid cache item key. " .
                "Supported keys can contain only characters a-z, 0-9, _, " .
                "and . (point) in any order."
            );
        }
    }

    /**
     * Get the list of deferred and saved items
     *
     * @return CacheItem[]
     */
    private function currentPoolCollection(): array
    {
        return array_merge($this->deferred, $this->items);
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
     *
     * @deprecated You SHOULD use the Slick\Cache\CacheSimpleStorageInterface
     */
    public function get($key, $default = false)
    {
        return $this->getItem($key);
    }

    /**
     * Set/stores a value with a given key.
     *
     * @param String $key The key where value will be stored.
     * @param mixed $value The value to store.
     * @param integer $duration The live time of cache in seconds.
     *
     * @return self A self instance for chaining method calls.
     *
     * @deprecated You SHOULD use the Slick\Cache\CacheSimpleStorageInterface
     */
    public function set($key, $value, $duration = null)
    {
        $item = $this->getItem($key);
        $item->set($value)->expiresAfter($duration);
        $this->save($item);
        return $this;
    }

    /**
     * Erase the value stored with a given key.
     *
     * You can use the "?" and "*" wildcards to delete all matching keys.
     * The "?" means a place holders for one unknown character, the "*" is
     * a place holder for various characters.
     *
     * Therefore this method is more expensive as it will grab all the keys
     * in the current pool and iterate over the ones that march your alias
     *
     * @param String $pattern The key alias to match
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function erase($pattern)
    {
        $regexp = str_replace(
            ['?', '*'],
            ['(.)', '(.*)'],
            "/^$pattern$/i"
        );
        $keys = $this->driver->getKeys();
        $toDelete = [];
        foreach ($keys as $key) {
            if (preg_match($regexp, $key)) $toDelete[] = $key;
        }

        return $this->deleteItems($toDelete);
    }

    /**
     * Flushes all values controlled by this pool
     *
     * This is an alias to CacheStorageInterface::clear()
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     *
     * @deprecated You SHOULD use the CacheStorageInterface::clear()
     */
    public function flush()
    {
        return $this->clear();
    }
}