<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache;

use Slick\Cache\Driver\CacheDriverInterface;
use Slick\Cache\Exception\InvalidDriverException;
use Slick\Common\Inspector;

/**
 * Cache storage factory
 *
 * @package Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Cache
{

    /**
     * Known cache driver classes
     */
    const CACHE_FILE = 'Slick\Cache\Driver\File';
    const CACHE_MEMORY = 'Slick\Cache\Driver\Memory';
    const CACHE_MEMCACHED = 'Slick\Cache\Driver\Memcached';
    const CACHE_NULL = 'Slick\Cache\Driver\Null';

    /**
     * Factory method to initialize a cache driver
     *
     * @param string $type
     * @param array  $options
     *
     * @throws InvalidDriverException
     *   If the provided class name (type) does not exists or it does not
     *   implement the CacheDriverInterface interface.
     *
     * @return CacheStorageInterface
     */
    public static function get($type = self::CACHE_FILE, $options = [])
    {
        $factory = new static();
        return $factory->createStorage(
            $factory->createDriver($type, $options)
        );
    }

    /**
     * Creates a cache driver from class name in type argument
     *
     * @param string $type    Driver class FQN
     * @param array  $options Driver constructor arguments
     *
     * @return CacheDriverInterface
     */
    protected function createDriver($type, $options)
    {
        $this->checkClassExists($type);
        $this->checkClassType($type);
        $driver = new $type($options);
        return $driver;
    }

    /**
     * Created a cache storage
     *
     * @param CacheDriverInterface $driver
     *
     * @return CacheStorage
     */
    protected function createStorage(CacheDriverInterface $driver)
    {
        return new CacheStorage($driver);
    }

    /**
     * Check if provided class exists
     *
     * @param string $class A FQ class name
     */
    protected function checkClassExists($class)
    {
        if (!class_exists($class)) {
            throw new InvalidDriverException(
                'Cache driver class not found.'
            );
        }
    }

    /**
     * Checks if provided class implements the CacheDriverInterface interface
     *
     * @param string $class A FQ class name
     */
    protected function checkClassType($class)
    {
        $reflection = Inspector::forClass($class)->getReflection();
        if (!$reflection->implementsInterface(CacheDriverInterface::class)) {
            throw new InvalidDriverException(
                "Class '{$class}' does not implements ".
                "Slick\\Cache\\CacheDriverInterface."
            );
        }
    }
}
