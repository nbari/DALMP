renumber
========

Some times you lost continuity on tables with auto increment fields, for
example instead of having a sequence like : 1 2 3 4 yo have something like: 1 5
18 30; in this cases, the method ``renumber('table')`` renumbers the table.


Parameters
..........

::

    renumber($table, $col='id')


:$table: name of the table to renumber.
:$col: name of the column with the **auto-increment** attribute.


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

Example where uid is the auto-increment column:

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


.. seealso::

   MySQL `AUTO_INCREMENT <http://dev.mysql.com/doc/refman/5.1/en/example-auto-increment.html>`_.