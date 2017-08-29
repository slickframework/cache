<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache;

use Prophecy\Argument;
use Psr\SimpleCache\CacheInterface;
use Slick\Cache\CacheDriverInterface;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheSimpleStorage;
use PhpSpec\ObjectBehavior;
use Slick\Cache\CacheSimpleStorageInterface;
use Slick\Cache\Exception\InvalidArgumentException;

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
        $driver->set(Argument::type('string'), Argument::type('string'), Argument::any())
            ->willReturn(true);
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

    function it_throws_an_invalid_argument_exception_when_setting_sith_invalid_key()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during('set', [':a{inv@lid}(key)\\/', 'foo']);
    }

    function it_fetches_a_value_from_cache(CacheDriverInterface $driver)
    {
        $value = 'bar';
        $ttl = 30;
        $key = 'test2';

        $item = new CacheItem($key, $value, $ttl);

        $driver->has($key)->willReturn(false);
        $driver->get($key)->shouldBeCalled()->willReturn(serialize($item));

        $this->set($key, $value, $ttl);

        $this->get($key)->shouldBe($value);
    }

    function it_returns_default_value_for_missing_cache_value(
        CacheDriverInterface $driver
    )
    {
        $key = 'test3';

        $item = new CacheItem($key);

        $driver->has($key)->willReturn(false);
        $driver->get($key)->shouldBeCalled()->willReturn(serialize($item));

        $this->get($key, 'foo')->shouldBe('foo');
    }

    function it_throws_an_invalid_argument_exception_when_key_is_not_valid()
    {
        $this->shouldThrow(InvalidArgumentException::class)
            ->during('get', [':a{inv@lid}(key)\\/']);
    }


    function it_check_if_a_given_key_exists(CacheDriverInterface $driver)
    {
        $key = 'test3';
        $driver->has($key)->shouldBeCalled()->willReturn(true);
        $this->has($key)->shouldBe(true);
    }

    function it_deletes_a_given_value_by_its_key(CacheDriverInterface $driver)
    {
        $key = 'test4';
        $driver->erase($key)->shouldBeCalled()->willReturn(true);
        $this->delete($key)->shouldBe(true);
    }

    function it_clears_all_the_cached_data(CacheDriverInterface $driver)
    {
        $driver->flush()->shouldBeCalled()->willReturn(true);
        $this->clear()->shouldBe(true);
    }

    function it_can_set_multiple_values(CacheDriverInterface $driver)
    {
        $key = 'test5';
        $value = 'foo';
        $expected = serialize(new CacheItem($key, $value));

        $driver->has($key)->willReturn(false);
        $driver->set($key, $expected, null)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->setMultiple([$key => $value])->shouldBe(true);
    }

    function it_can_get_multiple_values(CacheDriverInterface $driver)
    {
        $value = 'baz';
        $ttl = 20;
        $key = 'test6';

        $item = new CacheItem($key, $value, $ttl);

        $driver->has($key)->willReturn(false);
        $driver->get($key)->shouldBeCalled()->willReturn(serialize($item));

        $this->set($key, $value, $ttl);

        $this->getMultiple([$key])->shouldBe([$key => $value]);
    }

    function it_can_delete_multiple_values(CacheDriverInterface $driver)
    {
        $key = 'test7';
        $driver->erase($key)->shouldBeCalled()->willReturn(true);
        $this->deleteMultiple([$key])->shouldBe(true);
    }
}
