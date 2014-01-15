Cache
=====

The method ``Cache`` returns or instantiate a `DALMP\\Cache </en/latest/cache.html>`_ instance.

To use the cache features you must specify the type of cache either via DSN or
by passing a `DALMP\\Cache </en/latest/cache.html>`_ instance as an argument to the
method `useCache </en/latest/database/useCache.html>`_.

DSN Cache format
................


.. code-block:: rest

   charset://username:password@host:port/database?(type:host:port:compression)

:type: `Memcache </en/latest/cache/memcache.html>`_, `Redis </en/latest/cache/redis.html>`_, `Disk </en/latest/cache/disk.html>`_.
:host: The host of the Memcache, Redis server.
:port: The port of the Memcache, Redis server.
:compression: To use or not compression, only available for memcache.