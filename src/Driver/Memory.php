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
 * Memory cache
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Memory extends AbstractCacheDriver implements CacheDriverInterface
{

    private static $data = [];

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
        $source = array_key_exists($key, self::$data)
            ? self::$data[$key]
            : false;

        if ($source) {
            $item->set($source);
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
        self::$data[$item->getKey()] = $item->getData();
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
        unset(self::$data[$key]);
        return $this;
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return CacheDriverInterface|self|$this
     */
    public function flush()
    {
        self::$data = [];
        return $this;
    }
}