# Changelog

All Notable changes to `slick/cache` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## NEXT - YYYY-MM-DD


### Added
- PSR-6 Cache implementation
- Contributor Code of Conduct
- Issue template
- Pull request template

### Deprecated
- CacheStorageInterface::get() was moved to the new CacheSimpleStorageInterface::get() and
  deprecated. It is an alias of CacheStorageInterface::getItem()
- CacheStorageInterface::set() was moved to the new CacheSimpleStorageInterface::set() and
  deprecated. It is an alias of CacheStorageInterface::save()
- CacheStorageInterface::flush() will be removed in the next releases. 

### Fixed
- CacheStorageInterface change to fit the PSR-6 requirements
- Contributing documentation 

### Removed

### Security



## 1.2.1 - 2016-04-07

### Fixed
- Default cache duration was not set when storing cache item

## 1.2.0 - 2016-02-29

### Added
- First released stand alone cache package for Slick framework.