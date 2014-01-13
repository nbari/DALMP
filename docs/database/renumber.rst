renumber
========

Some times you lost continuity on tables with auto increment fields, for
example instead of having a sequence like : 1 2 3 4 yo have something like: 1 5
18 30; in this cases you can use renumber(table):


Parameters
..........

::

    renumber($table, $row='id')


:$table: name of the table to renumber.
:$row: name of the **auto-increment** row to apply the renumber method.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->renumber('table');

Example where uid is the auto-increment row:

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->renumber('table', 'uid');