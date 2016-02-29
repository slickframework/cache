# Slick Cache package

[![Latest Version](https://img.shields.io/github/release/slickframework/cache.svg?style=flat-square)](https://github.com/slickframework/cache/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/slickframework/cache/develop.svg?style=flat-square)](https://travis-ci.org/slickframework/cache)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/slickframework/cache/develop.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/cache/code-structure?branch=develop)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/cache/develop.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/cache?branch=develop)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/cache.svg?style=flat-square)](https://packagist.org/packages/slick/cache)


Slick cache package works with cache providing services installed on
your system.

It comes with support for *Memcached* (`memcached` daemon) and *File*
(caching data into files) out of the box, but it also defines a driver
interface that allows you to add your own drivers to your project.

## Install

Via Composer

``` bash
$ composer require slick/cache
```

Usage
--------------------

To use a cache driver you simple need to call the `Cache::get()` static
method to get an initialized cache driver. Check the following example:

```php
use Slick\Cache\Cache;

$cache = Cache::get();
$data = $cache->get('data', false);

if (!$data) {
    $data = file_get_contents("http://www.example.com/api/call.json");
    $cache->set('data', $data);
}
```    

In this example we are using the default cache driver with default
options, to store some expensive API call data.

> **note**
>
> The default driver is Memcached with the following default options:
>   * `duration => 120`
>   * `host => ‘127.0.0.1’`
>   * `port => 11211`
>

### Changing cache expire time

The expire amount of time is always set when you set a value on the
cache driver. As mention above, the default is set to 120 seconds. Using
the above example, we will set the time expire amount to 3 minutes for
the data from our fictitious API call:

```php
use Slick\Cache\Cache;

$cache = Cache::get();
$data = $cache->get('data', false);
if (!$data) {
    $data = file_get_contents("http://www.example.com/api/call.json");
    // Set expire to 3 minutes
    $cache->set('data', $data, 3*60);
}
```    

It is also possible to define a global expire amount of time for all
`Cache::set()` like this:

```php
use Slick\Cache\Cache;

$cache = Cache::get();
// Set global cache expire to 10 minutes
$cache->duration = 10*60;

$data = $cache->get('data', false);
if (!$data) {
    $data = file_get_contents("http://www.example.com/api/call.json");
    // This will use the 10 minutes setting from above
    $cache->set('data', $data);
}
```
  
### Slick\Cache\DriverInterface::set()
Set/stores a value with a given key. If no value is set in the
expire parameter the default `Cache::duration` will be used.

```php
public DriverInterface DriverInterface::set(string $key, mixed $value [, int $expire = -1])
```    

Parameters | Type | Description
---------- | ---- | -----------
$key | string | The key where value will be stored
$value | mixed | The value to store
$expire | int | The live time of cache in seconds

Return | Description
------ | -----------
Slick\Cache\DriverInterface | A `DriverInterface` instance for chaining method calls.

### Slick\Cache\DriverInterface::get()
Retrieves a previously stored value. You can optionally set the
value returned in case of cache driver has no value for provided
key.

```php
public mixed DriverInterface::get(string $key [, mixed $default = false])
```    
Parameters | Type | Description
---------- | ---- | -----------
$key | string | The key where value was stored
$default | mixed | The value returned if cache driver has no value for provided key

Return | Description
------ | -----------
mixed | The stored value or the default value if cache driver has no value for provided key

### Slick\Cache\DriverInterface::erase()

Erase the value stored with a given key. You can use the “?” and “*"
wildcards to delete all matching keys. The "?" means a place holders
for one unknown character, the "*” is a place holder for various

```php
public DriverInterface DriverInterface::erase(string $key)
```    

Parameters | Type | Description
---------- | ---- | -----------
$key | string | The key where value was stored

Return | Description
------ | -----------
Slick\Cache\DriverInterface | A `DriverInterface` instance for chaining method calls.

> **warning**
>
> The use of “?” and “\*” placeholder is only implemented in the drivers that are
> provided by Slick cache component. If you create your own cache
> driver you need to handle the placeholders key search
> implementation.
 

> **tip**
>
> If you are implementing your own cache driver and want to have the “?” and “\*”
> placeholders search you can extend `Slick\Cache\Driver\AbstractDriver` witch uses the
> `DriverInterface::get()` and `DriverInterface::set()` methods to achieve the wildcards
> key search feature.
>

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email silvam.filipe@gmail.com instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/database/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
