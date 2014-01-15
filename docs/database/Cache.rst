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


How to use
..........

Whenever you want to use the cache, just just need to prepend the word
**Cache** to the method you are using.

Parameters
..........

You can have finer control over your cached queries, for this you have the
following options::

    Cache[P]method(TTL, <query>, key or group)

:Cache[P]method: A normal or `prepared statements </en/latest/prepared_statements.html>`_ method: 'all, assoc, col, one, row'
:TTL: The time to live (timeout) in seconds for your query, default 3600 seconds / 1 hour if not set.
:query: A normal or prepared statements query.
:key or group: a unique key for storing the query result or the name of a caching group.

TTL example
...........

Cache the results for 300 seconds, 5 minutes:

.. code-block:: php
   :linenos:
   :emphasize-lines: 8, 14

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@localhost/dalmp?redis:127.0.0.1:6379';

   $db = new DALMP\Database($DSN);

   $db->FetchMode('ASSOC');

   $rs = $db->CacheGetAll(300, 'SELECT * FROM City');

   echo $rs, PHP_EOL;

Custom key example
..................

If you specify a custom key, the query result will be stored on the cache.

On the cache engine, the (key, value) is translated to:

:key: your custom key
:value: the output of your query

This is useful when you only want to flush certain parts of the cache, example:


.. code-block:: php
   :linenos:
   :emphasize-lines: 14

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@localhost/dalmp?redis:127.0.0.1:6379';

   $db = new DALMP\Database($DSN);

   $db->FetchMode('ASSOC');

   $rs = $db->CacheGetAll(300, 'SELECT * FROM City', 'my_custom_key');