<?php

/**
 * This file is part of sata/Cache
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Exception;

use InvalidArgumentException as PhpException;
use Psr\Cache\InvalidArgumentException as PsrException;
use Slick\Cache\Exception;

/**
 * InvalidArgumentException
 *
 * @package Slick\Cache\Exception
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class InvalidArgumentException extends PhpException implements Exception, PsrException
{

}
