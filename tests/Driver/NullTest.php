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
use Slick\Cache\Driver\NullDriver as NullDriver;

/**
 * Null cache driver test case
 *
 * @package Slick\Tests\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class NullTest extends TestCase
{

    /**
     * @var NullDriver
     */
    protected $driver;

    /**
     * Sets the SUT Null driver object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = new NullDriver();
    }

    /**
     * Cleanup for next test
     */
    protected function tearDown()
    {
        $this->driver = null;
        parent::tearDown();
    }

    /**
     * Should return an invalid cache item
     * @test
     */
    public function get()
    {
        $item = $this->driver->get('test');
        $this->assertFalse($item->isValid());
    }

    /**
     * Should return a self instance
     * @test
     */
    public function set()
    {
        $item = new CacheItem();
        $this->assertSame($this->driver, $this->driver->set($item));
    }

    /**
     * Should return a self instance
     * @test
     */
    public function erase()
    {
        $this->assertSame($this->driver, $this->driver->erase('test'));
    }

    /**
     * Should return a self instance
     * @test
     */
    public function flush()
    {
        $this->assertSame($this->driver, $this->driver->flush());
    }
}
