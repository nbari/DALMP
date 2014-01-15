CacheFlush
==========

Flush the cache, if no parameter specified it will flush all the cache.


Parameters
..........

::

    CacheFlush(sql or group, $key=null)

:sql or group: the query to flush or the group name
:$key: the custom key assigned to the query.


To flush / empty all the cache just call the CacheFlush with no parameteres,
example::

    $db->CacheFlush();

Examples
........

Flush a query:

.. code-block:: php
   :linenos:
   :emphasize-lines: 17

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@localhost/dalmp?redis:127.0.0.1:6379';

   $db = new DALMP\Database($DSN);

   $db->FetchMode('ASSOC');

   $rs = $db->CacheGetAll(300, 'SELECT * FROM City');

   // To flush the query
   $db->CacheFlush('SELECT * FROM City');


Flush a query with a custom key:

.. code-block:: php
   :linenos:

   <?php
   ...
   $rs = $db->CacheGetAll(300, 'SELECT * FROM City', 'my_custom_key');

   // To flush the query
   $db->CacheFlush('SELECT * FROM City', 'my_custom_key');


Flushing a chached group:

.. code-block:: php
   :linenos:
   :emphasize-lines: 7

   <?php
   ...
   $rs = $db->CachePGetAll('SELECT * FROM Country WHERE Population <= ?', 100000, 'group:B');
   $rs = $db->CachePGetAll(86400, 'SELECT * FROM Country WHERE Continent = ?', 'Europe', 'group:B');

   // To flush the query
   $db->CacheFlush('group:B');