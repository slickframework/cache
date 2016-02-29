<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Cache\Cache;
use Slick\Cache\CacheStorageInterface;

/**
 * Cache factory test
 *
 * @package Slick\Tests\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheTest extends TestCase
{

    /**
     * Should create a cache storage with file driver
     * @test
     */
    public function createStorage()
    {
        $cache = Cache::get();
        $this->assertInstanceOf(CacheStorageInterface::class, $cache);
    }

    /**
     * Should throw an exception
     *
     * @test
     * @expectedException \Slick\Cache\Exception\InvalidDriverException
     */
    public function unknownClass()
    {
        Cache::get('_Just_a_dummy_test_class_');
    }

    /**
     * Should throw an exception
     *
     * @test
     * @expectedException \Slick\Cache\Exception\InvalidDriverException
     */
    public function invalidClassType()
    {
        Cache::get('stdClass');
    }
}
