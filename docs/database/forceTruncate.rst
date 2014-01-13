forceTruncate
=============

Force truncate of a table either if are `InnoDB <http://en.wikipedia.org/wiki/InnoDB>`_.

Parameters
..........

::

   forceTable($table)

:$table: Name of the table to truncate.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';

   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->forceTrucate('mytable');