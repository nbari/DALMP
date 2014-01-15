Redis
=====

Implements the ``CacheInteface`` using as `redis.io <http://www.redis.io` as the cache backend.


__construct
...........

::

  __construct($host, $port, $timeout)

:$host: Point to the host where redis is listening for connections. This parameter may also specify other transports like unix:///path/to/redis.sock to use UNIX domain sockets - default 127.0.0.1.
:$port: Point to the port where redis is listening for connections - default 6379.
:$timeout: Value in seconds which will be used for connecting to the daemon. Think twice before changing the default value of 1 second - you can lose all the advantages of caching if your connection is too slow.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

    require_once 'dalmp.php';

    $cache = new DALMP\Cache\Redis('10.10.10.13', 6379);

    $cache->set('mykey', 'xpto', 300);

    $cache->get('mykey');

    $cache->X()->HSET('myhash', 'field1', 'hello'):

    $cache->X()->HGET('myhash', 'field1');

    $cache->X()->HGETALL('myhash');
