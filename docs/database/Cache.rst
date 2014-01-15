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

.. note::

   If no Cache is specified, defaults to `disk cache type </en/latest/cache/disk.html>`_.

The Cache methods
.................

The idea of using a 'cache' is to dispatch faster the results of a previous query
with out need to connect again to the database and fetch the results.

There are five methods you can use within the Cache method which are:

======== ============= ===================
method   Normal        Prepared statements
======== ============= ===================
`all`_   CacheGetAll   CachePGetAll
`assoc`_ CacheGetASSOC CachePGetASSOC
`col`_   CacheGetCol   CachePGetCol
`one`_   CacheGetOne   CachePGetOne
`row`_   CacheGetRow   CachePGetRow
======== ============= ===================

.. _all: /en/latest/database/getAll.html
.. _assoc: /en/latest/database/getASSOC.html
.. _col: /en/latest/database/getCol.html
.. _one: /en/latest/database/getOne.html
.. _row: /en/latest/database/getRow.html

.. note::

   Notice that when using "Cache" the methods are prefixed with
   **Cache**.

Constants
.........

::

   define('DALMP_CACHE_DIR', '/tmp/dalmp/cache/');

Defines where to store the cache when using `dir cache type </en/latest/cache/disk.html>`_.