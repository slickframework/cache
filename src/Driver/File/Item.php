<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver\File;

use DateTime;
use DateTimeZone;
use Slick\Common\Base;

/**
 * Cache item used with file cache driver
 *
 * @package Slick\Cache\Driver\File
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Item extends Base
{

    /**
     * @readwrite
     * @var DateTime
     */
    protected $expireDate;

    /**
     * @readwrite
     * @var mixed
     */
    protected $data;

    public static function fromString($serializedData)
    {
        $data = json_decode($serializedData);

    }
}
