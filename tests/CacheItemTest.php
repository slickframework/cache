<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheStorageInterface;

/**
 * Cache Item test case
 *
 * @package Slick\Tests\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheItemTest extends TestCase
{

    /**
     * @var CacheItem
     */
    protected $item;

    /**
     * Creates the SUT cache item object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->item = new CacheItem(['key' => 'test']);
    }

    public function dataSets()
    {
        return [
            'forever' => [['test'], CacheStorageInterface::CACHE_NEVER_EXPIRES],
            'never' => [['test'], CacheStorageInterface::CACHE_EXPIRED],
            'default' => [['test'], CacheStorageInterface::CACHE_DEFAULT],
        ];
    }

    /**
     * Should set the data and expiration date so that data validation is
     * properly set according to expire param.
     *
     * @test
     * @dataProvider dataSets
     *
     * @param mixed $data
     * @param int   $expire
     */
    public function setData($data, $expire)
    {
        $this->item->set($data, $expire);
        $this->assertTrue($this->item->isValid());
    }
}
