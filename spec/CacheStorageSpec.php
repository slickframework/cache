<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache;

use Prophecy\Argument;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheItemInterface;
use Slick\Cache\CacheDriverInterface;
use Slick\Cache\CacheStorage;
use PhpSpec\ObjectBehavior;
use Slick\Cache\CacheStorageInterface;
use Slick\Cache\Exception\InvalidArgumentException;

/**
 * CacheStorageSpec specs
 *
 * @package spec\Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheStorageSpec extends ObjectBehavior
{

    function let(CacheDriverInterface $driver)
    {
        $driver->set(
            Argument::type('string'),
            Argument::type('string'),
            Argument::any()
        )->willReturn(true);
        $item = new CacheItem('test3', 'test3', 3000);
        $driver->get('test3')->willReturn(serialize($item));
        $driver->has('test3')->willReturn(true);
        $driver->has(Argument::type('string'))->willReturn(false);
        $driver->getKeys()->willReturn(['test1', 'test2']);
        $driver->erase(Argument::type('string'))->willReturn(true);
        $this->beConstructedWith($driver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CacheStorage::class);
    }

    function its_a_cache_item_pool()
    {
        $this->shouldBeAnInstanceOf(CacheStorageInterface::class);
    }

    function it_returns_a_cache_item_represented_by_a_given_key()
    {
        $item = $this->getItem('test');
        $item->shouldBeAnInstanceOf(CacheItemInterface::class);
        $item->getKey()->shouldBe('test');
    }

    function it_returns_an_item_that_was_once_saved_or_loaded(CacheDriverInterface $driver)
    {
        $item = new CacheItem('test2');
        $this->save($item);

        $this->getItem('test2')->shouldBe($item);
        $driver->get('test2')->shouldNotHaveBeenCalled();
    }

    function it_loads_an_item_from_cache_driver(CacheDriverInterface $driver)
    {
        $item = $this->getItem('test3');
        $item->get()->shouldBe('test3');

        $driver->get('test3')->shouldHaveBeenCalled();
    }

    function it_throws_an_invalid_argument_exception_when_key_is_not_valid()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during('getItem', [':a{inv@lid}(key)\\/']);
    }

    function it_saves_a_cache_item_to_the_pool(
        CacheDriverInterface $driver
    )
    {
        $key = 'test';
        $item = new CacheItem($key);
        $serialized = serialize($item);

        $this->save($item)->shouldBe(true);

        $driver->set($key, $serialized, null)->shouldHaveBeenCalled();
    }

    function it_returns_a_list_of_cache_items()
    {
        $keys = ['test', 'test3'];
        $items = $this->getItems($keys);
        $items->shouldBeArray();
        $items['test3']->get()->shouldBe('test3');

    }

    function it_clears_all_items_in_the_pool(CacheDriverInterface $driver)
    {
        $this->save(new CacheItem('test4'));
        $this->hasItem('test4')->shouldBe(true);

        $result = true;
        $driver->flush()->shouldBeCalled()->willReturn($result);
        $this->clear()->shouldBe($result);

        $this->hasItem('test4')->shouldBe(false);
    }

    function it_deletes_an_item_from_the_pool(CacheDriverInterface $driver)
    {
        $this->save(new CacheItem('test4'));
        $this->hasItem('test4')->shouldBe(true);

        $driver->erase('test4')->shouldBeCalled()->willReturn(true);

        $this->deleteItem('test4')->shouldBe(true);

        $this->hasItem('test4')->shouldBe(false);
    }

    function it_deletes_a_list_on_items_from_the_pool(CacheDriverInterface $driver)
    {
        $this->save(new CacheItem('test5'));
        $this->save(new CacheItem('test6'));
        $this->hasItem('test5')->shouldBe(true);
        $this->hasItem('test6')->shouldBe(true);

        $driver->erase('test5')->shouldBeCalled()->willReturn(true);

        $this->deleteItems(['test5'])->shouldBe(true);

        $this->hasItem('test5')->shouldBe(false);
        $this->hasItem('test6')->shouldBe(true);
    }

    function it_sets_a_cache_item_to_be_persisted_later(CacheDriverInterface $driver)
    {
        $this->saveDeferred(new CacheItem('test8'))->shouldBe(true);

        $driver->set(
            Argument::type('string'),
            Argument::type('string'),
            Argument::any()
        )
            ->shouldNotHaveBeenCalled();
        $this->hasItem('test8')->shouldBe(true);
    }

    function it_persists_deferred_items(CacheDriverInterface $driver)
    {
        $key = 'test9';
        $item = new CacheItem($key);
        $serialized = serialize($item);
        $this->saveDeferred($item);

        $this->commit();

        $driver->set($key, $serialized, null)->shouldHaveBeenCalled();
    }

    function it_deletes_a_set_of_items_by_passing_a_key_pattern(CacheDriverInterface $driver)
    {
        $this->save(new CacheItem('test1'));
        $this->save(new CacheItem('test2'));

        $this->erase('t?st*')->shouldBe(true);

        $driver->getKeys()->shouldHaveBeenCalled();
        $driver->erase(Argument::type('string'))
            ->shouldHaveBeenCalledTimes(2);
    }
}
