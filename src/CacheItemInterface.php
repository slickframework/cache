<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use DateTime;

/**
 * Cache Item Interface
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface CacheItemInterface
{

    /**
     * Returns cache item key
     *
     * @return string
     */
    public function getKey();

    /**
     * Check if current data is valid
     *
     * @return bool
     */
    public function isValid();

    /**
     * Gets cached data
     *
     * If no data is present in this cache item this method SHOULD
     * return a null value.
     *
     * @return null|mixed
     */
    public function getData();

    /**
     * Sets item data
     *
     * @param mixed $data Serializable data
     *
     * @return CacheItemInterface|self|$this
     */
    public function setData($data);

    /**
     * Returns the date and time this cache item will expire
     *
     * @return DateTime
     */
    public function getExpirationDate();

    /**
     * Sets item expiration date
     *
     * @param DateTime $date
     * @return CacheItemInterface|self|$this
     */
    public function setExpirationDate(DateTime $date);

    /**
     * Sets cache data
     *
     * @param mixed $data
     * @param int $expire
     *
     * @return CacheItemInterface|self|$this
     */
    public function set($data, $expire = CacheStorageInterface::CACHE_DEFAULT);

    /**
     * Sets cache item identification key
     *
     * @param string $key
     *
     * @return CacheItemInterface|self|$this
     */
    public function setKey($key);
}