Disk
====

Implements the ``CacheInteface`` using as the hard disk as the cache backend.


__construct
...........

::

  __construct($path)

:$path: Directory to store the cache files - default ``/tmp/dalmp_cache``.

Constants
.........

::

   define('DALMP_CACHE_DIR', '/tmp/dalmp/cache/');

Defines where to store the cache when using 'dir' cache type.

This means that if no $path is passed as an argument to the **__construct**
before using the default value will try to get a path from the **DALMP_CACHE_DIR**
constant if set.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

    require_once 'dalmp.php';

    $cache = new DALMP\Cache\Disk('/tmp/my_cache_path')

    $cache->set('mykey', 'xpto', 300);

    $cache->get('mykey');

    $cache->stats();


.. seealso::

   `Cache Examples <https://github.com/nbari/DALMP/tree/master/examples/cache>`_.
