<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\CacheDriver;

use Slick\Cache\CacheDriverInterface;
use Slick\Cache\CacheItem;
use Slick\Cache\Exception\KeyNotFoundException;

/**
 * NullDriver
 *
 * @package Slick\Cache\CacheDriver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
*/
class NullDriver implements CacheDriverInterface
{
    /**
     * Stores a cache value
     *
     * @param string $key
     * @param string $serializedValue
     * @param \DateTimeImmutable $expires
     *
     * @return bool
     */
    public function set(string $key, string $serializedValue, \DateTimeImmutable $expires = null)
    {
        return true;
    }

    /**
     * Gets cache item saved with provided key
     *
     * @return string a serialized version of cached item
     */
    public function get(string $key): string
    {
        return serialize(new CacheItem($key));
    }

    /**
     * Check if there's an item cached under provided key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return bool
     */
    public function flush(): bool
    {
        return true;
    }

    /**
     * Erases cache item stored with provided key
     *
     * @param string $key
     *
     * @return bool
     */
    public function erase(string $key): bool
    {
        return true;
    }

    /**
     * Get all stored keys
     *
     * @return string[]
     */
    public function getKeys()
    {
        return [];
    }
}
