getOne / PGetOne
================

Executes the SQL and returns the first field of the first row. If an error
occurs, false is returned.

Parameters
..........

::

    getOne($sql)

:$sql: The MySQL query to perfom on the database

Prepared statements Parameters
..............................

::

    PgetOne($sql, $varN)

:$sql: The MySQL query to perfom on the database
:$varN: The variable(s) that will be placed instead of the **?** placeholder separated by a ',' or it can be the method `Prepare </en/latest/database/Prepare.html>`_.

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

   $rs = $db->PGetOne('SELECT * FROM Country WHERE Region = ? LIMIT 1', 'Caribbean');

Output of ``echo $rs``:

.. code-block:: rest
   :linenos:

   Aruba