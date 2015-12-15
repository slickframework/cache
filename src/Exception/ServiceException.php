<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Exception;

use Slick\Cache\Exception;

/**
 * Used when an error occurs trying to use a cache service
 *
 * @package Slick\Cache\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ServiceException extends \RuntimeException implements Exception
{

}
