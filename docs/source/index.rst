Slick Cache
==========================

``slick/cache`` works with cache providing services installed on your system.

It comes with support for Memcached (memcached daemon) and File (caching data
into files) out of the box, but it also defines a driver interface that allows
you to add your own drivers to your project.


This package has the `PSR-6`_ (Caching Interface) and the `PSR-16`_ (Common
Interface for Caching Libraries).


Installation
------------

`slick/cache` is a php 7.0+ library that you’ll have in your project development
environment. Before you begin, ensure that you have PHP 7.0 or higher installed.

You can install `slick/cache` with all its dependencies through Composer. Follow
instructions on the `composer website`_ if you don’t have it installed yet.

You can use this Composer command to install `slick/cache`:

.. code-block:: bash

    $ composer require slick/cache


.. toctree::
    :hidden:
    :maxdepth: 2

    manual/getting-started
    manual/contrib
    manual/license

.. _PSR-6: http://www.php-fig.org/psr/psr-6/
.. _PSR-16: http://www.php-fig.org/psr/psr-16/
.. _composer website: https://getcomposer.org/download/