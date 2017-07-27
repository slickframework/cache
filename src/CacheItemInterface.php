<?php

/**
 * This file is part of sata/Cache
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use DateTimeImmutable;
use Serializable;
use Psr\Cache\CacheItemInterface as PsrCacheItem;

/**
 * CacheItemInterface
 *
 * @package Slick\Cache
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface CacheItemInterface extends PsrCacheItem, Serializable
{

    /**
     * Get item expiration date
     *
     * @return DateTimeImmutable|null
     */
    public function expires();
}
