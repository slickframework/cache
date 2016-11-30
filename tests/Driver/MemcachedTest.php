<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache\Driver;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Cache\CacheItem;
use Slick\Cache\Driver\CacheDriverInterface;
use Slick\Cache\Driver\Memcached;

/**
 * Memcached cache driver test case
 *
 * @package Slick\Tests\Cache\Driver
 * @author  Filipe Silva <silvam.filiipe@gmail.com>
 */
class MemcachedTest extends TestCase
{

    /**
     * @var Memcached
     */
    protected $driver;

    /**
     * Sets the SUT cache driver object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = new Memcached();
    }

    /**
     * Cleanup for next test
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->driver = null;
    }

    /**
     * Should create a memcached driver with default values
     * @test
     */
    public function getDefaultMemcachedDriver()
    {
        $driver = $this->driver->server;
        $this->assertInstanceOf('Memcached', $driver);
    }

    /**
     * Should call Memcached::flush() method
     * @test
     */
    public function flushAllData()
    {
        $server = $this->getMemcachedMock(['flush']);
        $server->expects($this->once())
            ->method('flush')
            ->willReturn(true);
        $this->driver->server = $server;
        $this->assertSame($this->driver, $this->driver->flush());
    }

    /**
     * Should retrieve a valid item from memcached
     * @test
     */
    public function getCachedItem()
    {
        $data = ['test'];
        $key = 'test';
        $composed = 'cache-bin:test';

        $server = \Phake::mock(CacheDriverInterface::class);
        \Phake::when($server)->get($composed)->thenReturn($data);

        $this->driver->server = $server;
        $item = $this->driver->get($key);
        $this->assertEquals($data, $item->getData());
        $this->assertTrue($item->isValid());
    }

    /**
     * Should get an invalid Item
     * @test
     */
    public function getUnknownItem()
    {
        $key = 'test';
        $composed = 'cache-bin:test';
        $server = \Phake::mock(CacheDriverInterface::class);
        \Phake::when($server)->get($composed)->thenReturn(false);
        $this->driver->server = $server;
        $item = $this->driver->get($key);
        $this->assertNull($item->getData());
        $this->assertFalse($item->isValid());
    }

    /**
     * Should save an item to memcached server
     * @test
     */
    public function setACacheItem()
    {
        $item = new CacheItem(
            [
                'data' => ['test'],
                'key' => 'test'
            ]
        );
        $server = $this->getMemcachedMock(['set']);
        $server->expects($this->once())
            ->method('set')
            ->with('cache-bin:test', $item->getData(), 120)
            ->willReturn(true);
        $this->driver->server = $server;
        $this->assertSame($this->driver, $this->driver->set($item));
    }

    /**
     * Should apply the pattern and delete all matching key entries
     * @test
     */
    public function deleteAnItem()
    {
        $keys = [
            'cache-bin:user_1', 'cache-bin:article_1212'
        ];
        $key = 'article_*';
        $server = $this->getMemcachedMock(['getAllKeys', 'delete']);
        $server->expects($this->once())
            ->method('getAllKeys')
            ->willReturn($keys);
        $server->expects($this->once())
            ->method('delete')
            ->with('cache-bin:article_1212')
            ->willReturn(true);
        $this->driver->server = $server;
        $this->assertSame($this->driver, $this->driver->erase($key));
    }

    public function testEmptyKeysDelete()
    {
        $key = 'article_*';
        $server = $this->getMemcachedMock(['getAllKeys']);
        $server->expects($this->once())
            ->method('getAllKeys')
            ->willReturn(false);
        $this->driver->server = $server;
        $this->assertSame($this->driver, $this->driver->erase($key));
    }

    /**
     * Returns the memcached mocked object
     *
     * @return \Memcached|MockObject
     */
    protected function getMemcachedMock($methods)
    {
        /** @var MockObject|\Memcached $memcached */
        $memcached = $this->getMockBuilder('Memcached')
            ->setMethods($methods)
            ->getMock();
        return $memcached;
    }
}
