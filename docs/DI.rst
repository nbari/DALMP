Dependency injection
====================

**DALMP** offers a set of classes that allows you to work with
`MySQL </en/latest/database.html>`_,
`Cache </en/latest/database/Cache.html>`_ backends and
`sessions </en/latest/sessions.html>`_,
but when trying to use all this together, some
perfomance issues may be raised, so to deal with this, a DI
(`dependecy injector <http://en.wikipedia.org/wiki/Dependency_injection>`_) is high recomendable.

The idea of using a DI is to load once, and use any time with out need to
reload the objects.


abstractDI
..........

`abstractDI <https://github.com/nbari/DALMP/blob/master/src/DALMP/abstractDI.php>`_ is the name of an `abstrac class <http://www.php.net/manual/en/language.oop5.abstract.php>`_ that works as a base for building a dependecy injector.


.. note::

   The abstracDI class can be used as a base for any project not only **DALMP**

DI
==

`DI (Dependecy Injector) <https://github.com/nbari/DALMP/blob/master/src/DALMP/DI.php>`_
extends ``abstractDI`` and creates the DI for **DALMP**.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

   require_once 'dalmp.php';

   $di = new DALMP\DI();

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';
   $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
   $port = getenv('MYSQL_PORT') ?: '3306';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = $di->database($DSN);

   $cache = $di->cache($di->cache_redis('127.0.0.1', 6379));

   $sessions = $di->sessions($cache, 'sha512');

   $db->useCache($cache);

   $now = $db->CachegetOne('SELECT NOW()');

   echo $now;

   $sessions->regenerate_id(true);
