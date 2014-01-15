Memcache
========

Implements the ``CacheInteface`` using as `memcached <http://memcached.org/` as the cache backend.

Requires `Memcache PECL extension <http://pecl.php.net/package/memcache>`_.

__construct
...........

::

  __construct($host, $port, $timeout, $compress)

:$host: Point to the host where memcache is listening for connections. This parameter may also specify other transports like unix:///path/to/memcache.sock to use UNIX domain sockets - default 127.0.0.1.
:$port: Point to the port where memcache is listening for connections - default 11211.
:$timeout: Value in seconds which will be used for connecting to the daemon. Think twice before changing the default value of 1 second - you can lose all the advantages of caching if your connection is too slow.
:$compress: Enables or disables payload compression, Use `MEMCACHE_COMPRESSED <http://www.php.net/manual/en/memcache.set.php>`_ to store the item compressed (uses zlib).



Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

    require_once 'dalmp.php';

    $cache = new DALMP\Cache\Memcache('127.0.0.1', 11211, 1, 1);

    $cache->set('mykey', 'xpto', 300);

    $cache->get('mykey');

    $cache->X()->replace('mykey', 'otpx', false, 300);
