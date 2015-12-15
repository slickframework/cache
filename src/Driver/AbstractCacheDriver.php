<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver;

use Slick\Cache\CacheInterface;
use Slick\Common\BaseMethods;

/**
 * Wrapper for common properties and methods among cache drivers
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractCacheDriver implements CacheInterface
{
    /**
     * Used on property getters/setters
     */
    use BaseMethods;

    const CACHE_DEFAULT = -1;
    const CACHE_FOREVER = 0;

    /**
     * @readwrite
     * @var string The prefix for cache key
     */
    protected $prefix;

    /**
     * @readwrite
     * @var int The number of seconds until cache expiry
     */
    protected $duration = 120;
}
