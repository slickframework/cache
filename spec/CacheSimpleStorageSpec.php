<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache;

use Psr\SimpleCache\CacheInterface;
use Slick\Cache\CacheDriverInterface;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheSimpleStorage;
use PhpSpec\ObjectBehavior;
use Slick\Cache\CacheSimpleStorageInterface;

/**
 * CacheSimpleStorageSpec specs
 *
 * @package spec\Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheSimpleStorageSpec extends ObjectBehavior
{
    function let(CacheDriverInterface $driver)
    {
        $this->beConstructedWith($driver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CacheSimpleStorage::class);
    }

    function its_a_cache_simple_storage()
    {
        $this->shouldBeAnInstanceOf(CacheSimpleStorageInterface::class);
    }

    function its_an_implementation_of_psr_16()
    {
        $this->shouldBeAnInstanceOf(CacheInterface::class);
    }

    function it_can_write_a_value_under_a_provided_key(CacheDriverInterface $driver)
    {
        $expected = serialize(new CacheItem('test1', 'Its a test'));

        $driver->has('test1')->willReturn(false);
        $driver->set('test1', $expected, null)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->set('test1', 'Its a test')->shouldBe(true);
    }
}
