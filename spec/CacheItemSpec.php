<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\Cache;

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
    function it_is_initializable()
    {
        $this->shouldHaveType(CacheItem::class);
    }
}
