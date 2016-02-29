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
 * Null cache driver
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Null extends AbstractCacheDriver implements CacheDriverInterface
{

    /**
     * Gets cache item saved with provided key
     *
     * @param string $key
     *
     * @return CacheItemInterface
     */
    public function get($key)
    {
        return $this->getCacheItem()->setKey($key);
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
        return $this;
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return CacheDriverInterface|self|$this
     */
    public function flush()
    {
        return $this;
    }
}
