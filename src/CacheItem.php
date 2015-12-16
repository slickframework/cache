<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use DateTime;
use Slick\Common\Base;

/**
 * Cache Item
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property int $duration
 */
class CacheItem extends Base implements CacheItemInterface
{

    /**
     * @readwrite
     * @var string Cache item identification key
     */
    protected $key;

    /**
     * @readwrite
     * @var mixed Stored data
     */
    protected $data;

    /**
     * @readwrite
     * @var DateTime Cache expiration date
     */
    protected $expirationDate;

    /**
     * @readwrite
     * @var DateTime
     */
    protected $currentDate;

    /**
     * @readwrite
     * @var int
     */
    protected $duration = 120;

    /**
     * Returns cache item key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Check if current data is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getCurrentDate() <= $this->getExpirationDate();
    }

    /**
     * Gets cached data
     *
     * If no data is present in this cache item this method SHOULD
     * return a null value.
     *
     * @return null|mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the date and time this cache item will expire
     *
     * @return DateTime
     */
    public function getExpirationDate()
    {
        if (is_null($this->expirationDate)) {
            // Some date in the past. So this item will always fail.
            $this->expirationDate = new DateTime('1977-08-25 10:30:00');
        }
        return $this->expirationDate;
    }

    /**
     * Sets cache data
     *
     * @param mixed $data
     * @param int $expire
     *
     * @return CacheItemInterface|self|$this
     */
    public function set($data, $expire = CacheStorageInterface::CACHE_DEFAULT)
    {
        $this->setData($data);
        $currentDate = $this->getCurrentDate()->getTimestamp();
        switch ($expire) {
            case CacheStorageInterface::CACHE_DEFAULT:
                $duration = $this->duration;
                break;

            case CacheStorageInterface::CACHE_NEVER_EXPIRES:
                $duration = time() + 10*365*24*60*60;
                break;

            default:
                $duration = $expire;
                break;
        }
        $expirationDate = (new DateTime())
            ->setTimestamp($currentDate+$duration);
        return $this->setExpirationDate($expirationDate);
    }

    /**
     * Sets cache item identification key
     *
     * @param string $key
     *
     * @return CacheItemInterface|self|$this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Sets item expiration date
     *
     * @param DateTime $date
     * @return CacheItemInterface|self|$this
     */
    public function setExpirationDate(DateTime $date)
    {
        $this->expirationDate = $date;
        return $this;
    }

    /**
     * Sets item data
     *
     * @param mixed $data Serializable data
     *
     * @return CacheItemInterface|self|$this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Gets current datetime
     *
     * @return DateTime
     */
    protected function getCurrentDate()
    {
        if (null === $this->currentDate) {
            $this->currentDate = new DateTime('now');
        }
        return $this->currentDate;
    }
}