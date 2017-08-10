<?php
/**
 * This file is part of sata/Cache
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Functional;

use Slick\Cache\CacheDriver\MemcachedDriver;
use PHPUnit\Framework\TestCase;
use Slick\Cache\CacheStorage;

class MemcachedDriverTest extends TestCase
{

    /** @var  \Memcached */
    protected static $memcachedServer;

    /** @var  MemcachedDriver */
    protected $driver;

    /** @var  CacheStorage */
    protected $cache;

    public static function setUpBeforeClass()
    {
        self::$memcachedServer = new \Memcached();
        self::$memcachedServer->addServer('memcached', 11211);
        self::$memcachedServer->flush();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->driver = new MemcachedDriver(self::$memcachedServer);
        $this->cache = new CacheStorage($this->driver);
    }

    function testSaveValueToCache()
    {
        $item = $this->cache->getItem('test1');
        $item->set(['foo' => 'bar'])->expiresAfter(120);
        $this->cache->save($item);

        $cached = $this->cache->getItem('test1');
        $this->assertEquals(['foo' => 'bar'], $cached->get());
        $this->assertTrue($cached->isHit());
        $this->assertEquals(
            serialize($item),
            self::$memcachedServer->get('test1')
        );
    }

    function testLoadSavedValue()
    {
        $item = $this->cache->getItem('test1');
        $this->assertTrue($item->isHit());
    }

    function testErasePattern()
    {
        $this->cache->erase('t?s*');
        $item = $this->cache->getItem('test1');
        $this->assertFalse($item->isHit());
    }

    function testFlushAllValues()
    {
        $item = $this->cache->getItem('test2');
        $item->set(['foo' => 'bar'])->expiresAfter(120);
        $this->cache->save($item);

        $cached = $this->cache->getItem('test2');
        $this->assertTrue($cached->isHit());

        $this->cache->clear();

        $item = $this->cache->getItem('test2');
        $this->assertFalse($item->isHit());
    }
}
