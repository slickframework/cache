<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache;

use Psr\Cache\CacheItemInterface;
use Slick\Cache\CacheItem;
use PhpSpec\ObjectBehavior;

/**
 * CacheItemSpec specs
 *
 * @package spec\Slick\Cache
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CacheItemSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('key', 'value');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CacheItem::class);
    }

    function its_a_cache_item()
    {
        $this->shouldHaveType(CacheItemInterface::class);
    }

    function it_returns_the_key_of_current_cached_item()
    {
        $this->getKey()->shouldBe('key');
    }

    function it_retrieves_the_value_of_the_item()
    {
        $this->get()->shouldBe('value');
    }

    function it_returns_null_when_item_is_a_miss()
    {
        $this->expiresAt(new \DateTimeImmutable('-1 day'));
        $this->get()->shouldBeNull();
    }

    function it_confirms_cache_lookup_is_a_hit()
    {
        $this->isHit()->shouldBe(true);
    }

    function it_can_set_the_expiration_time_from_the_present()
    {
        $item = $this->expiresAfter(1);
        $item->shouldBe($this->getWrappedObject());
        sleep(2);
        $item->isHit()->shouldBe(false);
    }

    function it_can_set_the_exactly_expiration_date_time()
    {
        $inOneMin = (new \DateTimeImmutable('now'))->add(new \DateInterval('PT1M'));
        $this->expiresAt($inOneMin)->shouldBe($this->getWrappedObject());
        $this->isHit()->shouldBe(true);
    }

    function it_can_set_the_cached_value()
    {
        $object = (object) ['foo' => 2, 'bar' => 'test'];
        $this->set($object)->shouldBe($this->getWrappedObject());
        $this->get()->shouldBe($object);
    }

    function its_serializable()
    {
        $this->shouldHaveType(\Serializable::class);
        $deserialized = unserialize(serialize($this->getWrappedObject()));
        $this->get()->shouldBe($deserialized->get());
        $this->getKey()->shouldBe($deserialized->getKey());
        $this->isHit()->shouldBe($deserialized->isHit());
    }
}
