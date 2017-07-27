<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * CacheItem
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
*/
class CacheItem implements CacheItemInterface, \Serializable
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \DateTimeImmutable
     */
    private $expires;

    /**
     * Cache Item
     *
     * @param string $key
     * @param mixed $value
     * @param null|int|\DateInterval|\DateTimeInterface $expires
     */
    public function __construct(string $key, $value = null, $expires = null)
    {
        $this->key = $key;
        $this->value = $value;
        ($expires instanceof \DateTimeInterface)
            ? $this->expiresAt($expires)
            : $this->expiresAfter($expires);
    }

    /**
     * Returns the key for the current cache item.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        return $this->isHit()
            ? $this->value
            : null;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration.
     *   If none or null is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if (null === $time) {
            $this->expires = null;
            return $this;
        }

        $time = $time instanceof \DateInterval
            ? $time
            : new \DateInterval("PT{$time}S");


        $this->expires = $this->now()->add($time);

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt($expiration)
    {
        $this->expires = $expiration;
        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * @return bool
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        return null === $this->expires
            ? true
            : $this->now() <= $this->expires;
    }

    /**
     * Get current date time
     *
     * @return \DateTimeImmutable
     */
    private function now()
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    /**
     * Sets the value represented by this cache item.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            'key' => $this->key,
            'value' => $this->value,
            'expires' => $this->expires
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->key = $data['key'];
        $this->value = $data['value'];
        $this->expires = $data['expires'];
    }
}