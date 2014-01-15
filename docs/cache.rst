DALMP\\Cache
===========


The ``DALMP\Cache`` class works as a dispatcher for the current Cache classes, following  a common interface in order to maintain
compatibility with other **DALMP** classes.


Parameters
..........

::

  DALMP\Cache(object)

:object: An `CacheInterface instance </en/latest/cache/CacheInterface.html>`_.

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

.. toctree::
   :maxdepth: 2
   :includehidden:

   cache/CacheInterface
   cache/APC
   cache/disk
   cache/memcache
   cache/redis
