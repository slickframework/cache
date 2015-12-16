<?php

/**
 * This file is part of slick/cache package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Cache\Driver;

use League\Flysystem\FilesystemInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Cache\CacheItem;
use Slick\Cache\CacheItemInterface;
use Slick\Cache\Driver\File;
use Slick\Tests\Cache\Driver\Constraint\EncodedCacheItem;

/**
 * File cache driver test
 *
 * @package Slick\Tests\Cache\Driver
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class FileTest extends TestCase
{

    /**
     * @var File
     */
    protected $driver;

    /**
     * Creates the SUT file object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = new File;
    }

    /**
     * Cleanup for next test
     */
    protected function tearDown()
    {
        $this->driver = null;
        parent::tearDown();
    }

    /**
     * Should create a Filesystem object for system temp dir
     * @test
     */
    public function createFilesystemHandler()
    {
        $filesystem = $this->driver->filesystem;
        $this->assertInstanceOf(FilesystemInterface::class, $filesystem);
    }

    /**
     * Should create a file name with <bin>/<key>.tmp and save a json
     * serialized data representation.
     *
     * @test
     */
    public function setItem()
    {
        $filesystem = $this->getFilesystemMock();
        $item = $this->getTestCacheItem();
        $filesystem->expects($this->once())
            ->method('put')
            ->with('cache-bin/test.tmp', $this->isEncodedCacheItem($item))
            ->willReturn(true);

        $this->driver->filesystem = $filesystem;
        $this->driver->set($item);
    }

    /**
     * Should throw an exception if filesystem fails to write
     * the cache item data.
     *
     * @test
     * @expectedException \Slick\Cache\Exception\ServiceException
     */
    public function setItemFailure()
    {
        $filesystem = $this->getFilesystemMock();
        $filesystem->method('put')
            ->willReturn(false);
        $this->driver->filesystem = $filesystem;
        $this->driver->set($this->getTestCacheItem());
    }

    /**
     * Should delete the file with provided key
     *
     * @test
     */
    public function eraseItem()
    {
        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('has')
            ->with('cache-bin/test.tmp')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $this->driver->filesystem = $filesystem;
        $this->driver->erase('test');
    }

    /**
     * Should delete all the files in the <bin> directory
     *
     * @test
     */
    public function flush()
    {
        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('has')
            ->with('cache-bin')
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('deleteDir')
            ->willReturn(true);
        $this->driver->filesystem = $filesystem;
        $this->driver->flush();
    }

    /**
     * Should return a cache item with null data.
     *
     * @test
     */
    public function getUnknownItem()
    {
        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('has')
            ->with('cache-bin/test.tmp')
            ->willReturn(false);
        $this->driver->filesystem = $filesystem;
        $item = $this->driver->get('test');
        $this->assertEquals('test', $item->getKey());
    }

    /**
     * Should read the file named <bin>/<key>.tmp and return a
     * valid cached item.
     *
     * @test
     */
    public function getValidCachedItem()
    {
        $fileName = 'cache-bin/test.tmp';
        $item = $this->getTestCacheItem();
        $filesystem = $this->getFilesystemMock();
        $filesystem->expects($this->once())
            ->method('has')
            ->with($fileName)
            ->willReturn(true);
        $filesystem->expects($this->once())
            ->method('read')
            ->with($fileName)
            ->willReturn($this->getEncodedItem($item));
        $this->driver->filesystem = $filesystem;
        $fromCache = $this->driver->get('test');
        $this->assertEquals($item, $fromCache);
    }

    /**
     * Generates a filesystem mock object
     *
     * @return FilesystemInterface|MockObject
     */
    protected function getFilesystemMock()
    {
        $class = FilesystemInterface::class;
        $methods = get_class_methods($class);

        /** @var FilesystemInterface|MockObject $filesystem */
        $filesystem = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $filesystem;
    }

    /**
     * Returns a EncodedCacheItem matcher object.
     *
     * @param CacheItemInterface $item
     * @return EncodedCacheItem
     */
    protected function isEncodedCacheItem(CacheItemInterface $item)
    {
        return new EncodedCacheItem($item);
    }

    /**
     * Gets a cache item object
     *
     * @return CacheItem
     */
    protected function getTestCacheItem()
    {
        $date = new \DateTime('now');
        $item = new CacheItem(
            [
                'key' => 'test',
                'expirationDate' => $date,
                'data' => ['test']
            ]
        );
        return $item;
    }

    /**
     * Gets a serialization of test item
     *
     * @param CacheItemInterface $item
     *
     * @return string
     */
    protected function getEncodedItem(CacheItemInterface $item)
    {
        $data = (object)[
            'expires' => $item->getExpirationDate()->format('c'),
            'data' => serialize($item->getData())
        ];
        return json_encode($data);
    }
}
