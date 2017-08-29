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
 * MemcachedDriver
 *
 * @package Slick\Cache\CacheDriver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
*/
class MemcachedDriver implements CacheDriverInterface
{
    /**
     * @var \Memcached
     */
    private $server;

    /**
     * MemcachedDriver
     *
     * @param \Memcached $server
     */
    public function __construct(\Memcached $server)
    {
        $this->server = $server;
    }

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
        $this->server->set($key, $serializedValue, $this->calculateExpire($expires));
        return true;
    }

    /**
     * Gets cache item saved with provided key
     *
     * You should check value existence with CacheDriverInterface::has() before
     * calling this method, otherwise an exception will be thrown.
     *
     * @param string $key
     *
     * @throws KeyNotFoundException
     *      If the key was not found or previously saved.
     *
     * @return string a serialized version of cached item
     */
    public function get(string $key): string
    {
        $default = serialize(new CacheItem($key));
        $value = $this->server->get($key);

        return $value === false
            ? $default
            : $value;
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
        return (boolean) $this->server->get($key);
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return bool
     */
    public function flush(): bool
    {
        $this->server->flush();
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
        return $this->server->delete($key, 0);
    }

    /**
     * Get all stored keys
     *
     * @return string[]
     */
    public function getKeys()
    {
        $keys = $this->server->getAllKeys();
        return $keys === false ? [] : $keys;
    }

    /**
     * Get the expiration value as defined by Memcached extension
     *
     * @see http://php.net/manual/en/memcached.expiration.php
     *
     * @param \DateTimeImmutable|null $expires
     * @return int|null
     */
    private function calculateExpire(\DateTimeImmutable $expires = null)
    {
        if (! $expires instanceof \DateTimeImmutable) {
            return null;
        }

        $now = new \DateTimeImmutable('now');
        $diff = $expires->getTimestamp() - $now->getTimestamp();

        return (60*60*24*30) > $diff
            ? $diff
            : $expires->getTimestamp();
    }
}
