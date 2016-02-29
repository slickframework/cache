<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver;

use Slick\Cache\CacheItemInterface;

/**
 * Memcached cache driver
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string $host
 * @property int    $port
 * @property string $instanceKey
 *
 * @property \Memcached $server
 */
class Memcached extends AbstractCacheDriver implements CacheDriverInterface
{

    /**
     * @readwrite
     * @var \Memcached
     */
    protected $server;

    /**
     * @readwrite
     * @var string
     */
    protected $host = 'localhost';

    /**
     * @readwrite
     * @var string
     */
    protected $port = 11211;

    /**
     * @readwrite
     * @var string
     */
    protected $instanceKey = 'mmc';

    /**
     * Gets cache item saved with provided key
     *
     * @param string $key
     *
     * @return CacheItemInterface
     */
    public function get($key)
    {

        $item = $this->getCacheItem();
        $source = $this->getServer()
            ->get($this->getComposedKey($key));
        if ($source) {
            $item->set($source, 2);
        }
        return $item->setKey($key);
    }

    /**
     * Stores provided cache item
     *
     * @param CacheItemInterface $item
     *
     * @return CacheDriverInterface|self|$this
     */
    public function set(CacheItemInterface $item)
    {
        $this->getServer()->set(
            $this->getComposedKey($item->getKey()),
            $item->getData(),
            $this->duration
        );
        return $this;
    }

    /**
     * Erases cache item stored with provided key
     *
     * @param string $key
     *
     * @return CacheDriverInterface|self|$this
     */
    public function erase($key)
    {
        $keys = $this->getServer()->getAllKeys();
        if ($keys === false) {
            return $this;
        }

        $name = str_replace(
            ['?', '*'],
            ['.', '.*'],
            $key
        );
        $rep = "/^{$this->bin}:({$name})$/i";

        foreach ($keys as $sKey) {
            if (preg_match($rep, $sKey)) {
                $this->getServer()
                    ->delete($sKey);
            }
        }
        return $this;
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return CacheDriverInterface|self|$this
     */
    public function flush()
    {
        $this->getServer()->flush();
        return $this;
    }

    /**
     * Creates a server with current properties set
     *
     * @return \Memcached
     */
    protected function getServer()
    {
        if ($this->server == null) {
            $this->server = new \Memcached($this->instanceKey);
            $this->server->addServer($this->host, $this->port);
        }
        return $this->server;
    }

    /**
     * Sets key name attached to cache bin and returns it
     *
     * @param string $key
     *
     * @return string
     */
    protected function getComposedKey($key)
    {
        return "{$this->bin}:$key";
    }

}
