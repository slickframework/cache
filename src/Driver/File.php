<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Cache\Driver;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheItemInterface;
use Slick\Cache\Exception\ServiceException;
use Slick\Common\Base;

/**
 * Uses file system to store cache data
 *
 * @package Slick\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string     $path
 * @property Filesystem $filesystem
 * @property string     $bin
 * @property string     $cacheItemClass
 * @property int        $duration
 */
class File extends Base implements CacheDriverInterface
{

    /**
     * @readwrite
     * @var string
     */
    protected $path;

    /**
     * @readwrite
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @readwrite
     * @var int
     */
    protected $duration = 120;

    /**
     * @readwrite
     * @var string
     */
    protected $bin = 'cache-bin';

    /**
     * @readwrite
     * @var string
     */
    protected $cacheItemClass = 'Slick\Cache\CacheItem';

    /**
     * Lazy loads filesystem based on File::$path property
     *
     * @return Filesystem
     */
    protected function getFilesystem()
    {
        if (null === $this->filesystem) {
            $adapter = new Local($this->getPath());
            $this->filesystem = new Filesystem($adapter);
        }
        return $this->filesystem;
    }

    /**
     * Gets cache item saved with provided key
     *
     * @param string $key
     *
     * @return CacheItemInterface
     */
    public function get($key)
    {
        $data = false;
        $name = $this->getFileName($key);
        if ($this->getFilesystem()->has($name)) {
            $data = $this->filesystem->read($name);
        }
        return $this->decode($data)->setKey($key);
    }

    /**
     * Stores provided cache item
     *
     * @param CacheItemInterface $item
     *
     * @return CacheDriverInterface|self|$this
     */
    public function set(CacheItemInterface $item)
    {
        $result = $this->getFilesystem()
            ->put(
                $this->getFileName($item->getKey()),
                $this->encode($item)
            );

        if (!$result) {
            throw new ServiceException("Error trying to save cache data.");
        }

        return $this;
    }

    /**
     * Erases cache item stored with provided key
     *
     * @param string $key
     *
     * @return CacheDriverInterface|self|$this
     */
    public function erase($key)
    {
        $name = $this->getFileName($key);
        if ($this->getFilesystem()->has($name)) {
            $this->getFilesystem()->delete($name);
        }
        return $this;
    }

    /**
     * Deletes all saved items in this cache bin
     *
     * @return CacheDriverInterface|self|$this
     */
    public function flush()
    {
        $exists = $this->getFilesystem()->has($this->bin);
        if ($exists) {
            $this->getFilesystem()->deleteDir($this->bin);
        }
        return $this;
    }

    /**
     * Creates file name for provided Cache item
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFileName($key)
    {
        return "{$this->bin}/{$key}.tmp";
    }

    /**
     * Encodes cache item preparing it to be saved
     *
     * @param CacheItemInterface $item
     *
     * @return string
     */
    protected function encode(CacheItemInterface $item)
    {
        $data = (object) [
            'expires' => $item->getExpirationDate()->format('c'),
            'data' => serialize($item->getData())
        ];
        return json_encode($data);
    }

    /**
     * Decode provided data
     *
     * @param string $data Serialized data
     *
     * @return CacheItem
     */
    protected function decode($data)
    {
        $item = $this->getCacheItem();
        if ($data !== false) {
            $source = json_decode($data);
            $item->setExpirationDate(new \DateTime($source->expires))
                ->setData(unserialize($source->data));
        }
        return $item;
    }

    /**
     * Returns an empty cache item
     *
     * @return CacheItemInterface
     */
    public function getCacheItem()
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = new $this->cacheItemClass();
        $cacheItem->duration = $this->duration;
        return $cacheItem;
    }

    /**
     * Returns cache root path
     *
     * @return string
     */
    protected function getPath()
    {
        if (null === $this->path) {
            $this->path = sys_get_temp_dir();
        }
        return $this->path;
    }
}