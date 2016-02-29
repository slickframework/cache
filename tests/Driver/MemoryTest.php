<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache\Driver;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Cache\CacheItem;
use Slick\Cache\Driver\Memory;

/**
 * Memory cache driver test case
 *
 * @package Slick\Tests\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class MemoryTest extends TestCase
{

    /**
     * @var Memory
     */
    protected $driver;

    /**
     * Sets the SUT memory driver object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = new Memory();
    }

    public function testSet()
    {
        $item = new CacheItem(
            [
                'key' => 'test',
                'data' => 'I am a value'
            ]
        );
        $this->assertSame($this->driver, $this->driver->set($item));
    }

    public function testGet()
    {
        $this->assertEquals('I am a value', $this->driver->get('test')->getData());
    }

    public function testErase()
    {
        $this->driver->erase('test');
        $item = $this->driver->get('test');
        $this->assertFalse($item->isValid());
    }

    public function testFlush()
    {
        $item = new CacheItem(
            [
                'key' => 'test',
                'data' => 'I am a value'
            ]
        );
        $this->driver->set($item);
        $this->driver->flush();
        $item = $this->driver->get('test');
        $this->assertFalse($item->isValid());
    }
}
