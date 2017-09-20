.. title:: Getting started: Slick Cache

Getting started
===============

Caching is a common way to improve performance and its present in almost
every framework. Slick has one too!

``slick/cache`` implements the PSR-6 and PSR-16 cache interfaces and its easy
to add it to your project. It also has a simple ``CacheDriverInterface`` interface
that let you implement your integration with any cache backend you may need to use.

In most cases you will only need to use a simple (PSR-16 Common Interface for
aching Libraries) ``CacheStorage`` as its a standardized streamlined interface for
common cases. Nevertheless it has been designed to make compatibility with PSR-6 as
straightforward as possible.

Lets get started!

Basic usage
-----------



.. _composer website: https://getcomposer.org/download/
