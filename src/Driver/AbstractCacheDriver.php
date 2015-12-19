<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver;

use Slick\Cache\CacheItem;
use Slick\Cache\CacheItemInterface;
use Slick\Common\Base;

/**
 * Abstract Cache Driver provides common properties and methods for
 * cache drivers
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string     $bin
 * @property string     $cacheItemClass
 * @property int        $duration
 */
abstract class AbstractCacheDriver extends Base implements CacheDriverInterface
{

    /**
     * @readwrite
     * @var int
     */
    protected $duration = 120;

    /**
     * @readwrite
     * @var string
     */
    protected $bin = 'cache-bin';

    /**
     * @readwrite
     * @var string
     */
    protected $cacheItemClass = 'Slick\Cache\CacheItem';

    /**
     * Returns an empty cache item
     *
     * @return CacheItemInterface
     */
    public function getCacheItem()
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = new $this->cacheItemClass();
        $cacheItem->duration = $this->duration;
        return $cacheItem;
    }
}
