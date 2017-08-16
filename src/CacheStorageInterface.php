<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache Storage Interface
 *
 * This interface was created to maintain backwards compatibility and it
 * will use the pool interface to do its job.
 *
 * This is basically an CacheItemPool interface
 *
 * @package Slick\Cache
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface CacheStorageInterface extends CacheItemPoolInterface
{


    /**
     * Retrieves a previously stored value.
     *
     * @param String $key     The key under witch value was stored.
     * @param mixed  $default The default value, if no value was stored before.
     *
     * @return mixed
     *  The stored value or the default value if it was not found
     *  on service cache.
     *
     * @deprecated You SHOULD use the Slick\Cache\CacheSimpleStorageInterface
     */
    public function get($key, $default = false);

    /**
     * Set/stores a value with a given key.
     *
     * @param String  $key      The key where value will be stored.
     * @param mixed   $value    The value to store.
     * @param integer $duration The live time of cache in seconds.
     *
     * @return self A self instance for chaining method calls.
     *
     * @deprecated You SHOULD use the Slick\Cache\CacheSimpleStorageInterface
     */
    public function set($key, $value, $duration = null);

    /**
     * Erase the value stored with a given key.
     *
     * You can use the "?" and "*" wildcards to delete all matching keys.
     * The "?" means a place holders for one unknown character, the "*" is
     * a place holder for various characters.
     *
     * Therefore this method is more expensive as it will grab all the keys
     * in the current pool and iterate over the ones that march your alias
     *
     * @param String $pattern The key alias to match
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function erase($pattern);

    /**
     * Flushes all values controlled by this pool
     *
     * This is an alias to CacheStorageInterface::clear()
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     *
     * @deprecated You SHOULD use the CacheStorageInterface::clear()
     */
    public function flush();
}
