<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver;

use Slick\Cache\CacheInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

/**
 * Uses file system to store cache data
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class File extends AbstractCacheDriver implements CacheInterface
{

    /**
     * @var string
     */
    private $path;

    /**
     * @readwrite
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * File cache driver with a path to the directory where to save the data
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Retrieves a previously stored value.
     *
     * @param String $key The key under witch value was stored.
     * @param mixed $default The default value, if no value was stored before.
     *
     * @return mixed
     *  The stored value or the default value if it was not found
     *  on service cache.
     */
    public function get($key, $default = false)
    {
        // TODO: Implement get() method.
    }

    /**
     * Set/stores a value with a given key.
     *
     * @param String $key The key where value will be stored.
     * @param mixed $value The value to store.
     * @param integer $duration The live time of cache in seconds.
     *
     * @return self A self instance for chaining method calls.
     */
    public function set($key, $value, $duration = -1)
    {
        // TODO: Implement set() method.
    }

    /**
     * Erase the value stored with a given key.
     *
     * You can use the "?" and "*" wildcards to delete all matching keys.
     * The "?" means a place holders for one unknown character, the "*" is
     * a place holder for various characters.
     *
     * @param String $key The key under witch value was stored.
     *
     * @return self A self instance for chaining method calls.
     */
    public function erase($key)
    {
        // TODO: Implement erase() method.
    }

    /**
     * Flushes all values controlled by this cache driver
     *
     * @return self A self instance for chaining method calls.
     */
    public function flush()
    {
        // TODO: Implement flush() method.
    }

    /**
     * Lazy loads filesystem based on File::$path property
     *
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if (null === $this->filesystem) {
            $adapter = new Local($this->path);
            $this->filesystem = new Filesystem($adapter);
        }
        return $this->filesystem;
    }

    /**
     * Calculates the duration for a cache item based on value user
     * may define for it.
     *
     * @see CacheInterface::set()
     *
     * @param integer $duration The live time of cache in seconds.
     */
    protected function calculate($duration)
    {

    }
}