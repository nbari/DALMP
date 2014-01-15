useCache
========

Set a `DALMP\\Cache </en/latest/cache.html>`_ instance to use.


Parameters
..........

::

    useCache($cache)

:$cache: An `DALMP\\Cache </en/latest/cache.html>`_ instance.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 12, 14

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@localhost/test';

   $db = new DALMP\Database($DSN);

   $cache = new DALMP\Cache(new DALMP\Cache\Memcache());

   $db->useCache($cache);

   $rs = $db->CacheGetOne('SELECT now()');

   echo $rs, PHP_EOL;

.. seealso::

   `Cache method </en/latest/database/Cache.html>`_