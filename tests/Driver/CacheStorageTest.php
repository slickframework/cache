<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache\Driver;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheStorage;
use Slick\Cache\Driver\CacheDriverInterface;

/**
 * Cache Storage test case
 *
 * @package Slick\Tests\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheStorageTest extends TestCase
{

    /**
     * @var CacheStorage
     */
    protected $cache;

    /**
     * @var CacheDriverInterface|MockObject
     */
    protected $driver;

    /**
     * Creates the SUT driver object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = $this->getMockDriver();
        $this->cache = new CacheStorage($this->driver);
    }

    /**
     * Should run driver flush method
     * @test
     */
    public function flush()
    {
        $this->driver->expects($this->once())
            ->method('flush');
        $this->cache->flush();
    }

    /**
     * Should call erase with key argument
     *
     * @test
     */
    public function erase()
    {
        $key = 'test';
        $this->driver->expects($this->once())
            ->method('erase')
            ->with($key);
        $this->cache->erase($key);
    }

    /**
     * Should return the default value.
     *
     * @test
     */
    public function getInvalidItem()
    {
        $key = 'test';
        $item = new CacheItem(['key' => $key]);
        $this->driver->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($item);
        $this->assertFalse($this->cache->get($key));
    }

    /**
     * Should return the saved data
     * @test
     */
    public function getCachedItem()
    {
        $key = 'test';
        $data = (object)['test'];
        $item = new CacheItem(['key' => $key]);
        $item->set($data, 3600);
        $this->driver->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn($item);
        $this->assertSame($data, $this->cache->get($key));
    }

    /**
     * Should create and run driver set of a new cache item
     *
     * @test
     */
    public function setCacheItem()
    {
        $item = new CacheItem();
        $this->driver->expects($this->once())
            ->method('getCacheItem')
            ->willReturn($item);
        $this->driver->expects($this->once())
            ->method('set')
            ->with($item);
        $this->cache->set('test', ['test'], 3600);
    }

    /**
     * Gets mock driver
     * @return MockObject|CacheDriverInterface
     */
    protected function getMockDriver()
    {
        $class = CacheDriverInterface::class;
        $methods = get_class_methods($class);
        /** @var CacheDriverInterface|MockObject $driver */
        $driver = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $driver;
    }
}
