<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache\CacheDriver;

use Slick\Cache\CacheDriver\NullDriver;
use PhpSpec\ObjectBehavior;
use Slick\Cache\CacheDriverInterface;
use Slick\Cache\Exception\KeyNotFoundException;

/**
 * NullDriverSpec specs
 *
 * @package spec\Slick\Cache\CacheDriver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class NullDriverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NullDriver::class);
    }

    function its_a_cache_driver()
    {
        $this->shouldBeAnInstanceOf(CacheDriverInterface::class);
    }

    function it_does_not_store_any_cache_values()
    {
        $this->getKeys()->shouldBe([]);
    }

    function it_returns_true_when_erasing()
    {
        $this->erase('test')->shouldBe(true);
    }

    function it_returns_true_when_flushing()
    {
        $this->flush()->shouldBe(true);
    }

    function it_returns_false_when_getting_values()
    {
        $this->shouldThrow(KeyNotFoundException::class)
            ->during('get', ['test']);
    }

    function it_returns_always_true_on_setting_values()
    {
        $this->set('test', 'test')->shouldBe(true);
    }

    function it_returns_false_on_checking_existence()
    {
        $this->has('test')->shouldBe(false);
    }
}
