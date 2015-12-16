<?php
/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 16-12-2015
 * Time: 16:14
 */

namespace Slick\Tests\Cache\Driver\Constraint;

use PHPUnit_Framework_Constraint as Constraint;
use Slick\Cache\CacheItemInterface;

class EncodedCacheItem extends Constraint
{

    /**
     * @var CacheItemInterface
     */
    protected $item;

    /**
     * EncodedCacheItem constructor.
     *
     * @param CacheItemInterface $item
     */
    public function __construct(CacheItemInterface $item)
    {
        parent::__construct();
        $this->item = $item;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "is a valid cache item serialization";
    }

    public function matches($other)
    {
        return $other == $this->encode($this->item);
    }

    /**
     * Encodes provided item
     *
     * @param CacheItemInterface $item
     * @return string
     */
    protected function encode(CacheItemInterface $item)
    {
        $data = (object) [
            'expires' => $item->getExpirationDate()->format('c'),
            'data'=> serialize($item->getData())
        ];
        return json_encode($data);
    }
}