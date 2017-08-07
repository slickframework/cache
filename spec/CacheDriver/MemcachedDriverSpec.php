<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache\CacheDriver;

use Slick\Cache\CacheDriver\MemcachedDriver;
use PhpSpec\ObjectBehavior;
use Slick\Cache\CacheDriverInterface;
use Slick\Cache\CacheItem;

/**
 * MemcachedDriverSpec specs
 *
 * @package spec\Slick\Cache\CacheDriver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class MemcachedDriverSpec extends ObjectBehavior
{
    function let(\Memcached $memcachedServer)
    {
        $this->beConstructedWith($memcachedServer);
    }

    function it_is_initializable_with_a_memcached_instance()
    {
        $this->shouldHaveType(MemcachedDriver::class);
    }

    function its_a_cache_driver()
    {
        $this->shouldHaveType(CacheDriverInterface::class);
    }

    function it_set_a_value_in_the_cache(\Memcached $memcachedServer)
    {
        $value = 'test';
        $this->set('test', $value)->shouldBe(true);
        $memcachedServer->set('test', $value, null)
            ->shouldHaveBeenCalled();
    }

    function it_set_a_value_with_expiration_date(
        \Memcached $memcachedServer
    )
    {
        $date = (new \DateTimeImmutable('now'))->add(new \DateInterval('PT80S'));
        $this->set('test', 'test', $date);
        $memcachedServer->set('test', 'test', 80)
            ->shouldHaveBeenCalled();
    }

    function it_gets_a_value_from_the_cache(\Memcached $memcachedServer)
    {
        $memcachedServer->get('test')->shouldBeCalled()
            ->willReturn('hello');
        $this->get('test')->shouldBe('hello');
    }

    function it_returns_a_default_cache_item_if_not_found(\Memcached $memcachedServer)
    {
        $memcachedServer->get('test')->shouldBeCalled()
            ->willReturn(false);
        $expected = serialize(new CacheItem('test'));
        $this->get('test')->shouldBe($expected);
    }

    function it_checks_the_existence_of_a_given_value(\Memcached $memcachedServer)
    {
        $memcachedServer->get('test')->willReturn('test');
        $this->has('test')->shouldBe(true);
    }

    function it_can_erase_a_given_value(\Memcached $memcachedServer)
    {
        $this->erase('test')->shouldBe(true);
        $memcachedServer->delete('test')->shouldHaveBeenCalled();
    }

    function it_can_flush_all_saved_values(\Memcached $memcachedServer)
    {
        $this->flush()->shouldBe(true);
        $memcachedServer->flush()->shouldHaveBeenCalled();
    }

    function it_returns_all_keys_from_stored_values(\Memcached $memcachedServer)
    {
        $values = [];
        $memcachedServer->getAllKeys()->willReturn($values);
        $this->getKeys()->shouldBe($values);
    }
}
