Execute
=======

Execute an SQL statement, returns true on success, false if there was an error
in executing the sql.

Parameters
..........

::

    Execute($sql)

:$sql: The MySQL query to perform on the database

In most cases you only use this method when Inserting or Updating data, for
retrieving data the 5 common methods are:

+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| Name | Normal                                          | Prepared Statements | Cache Normal  | Cache Prepared Statements |
+======+=================================================+=====================+===============+===========================+
| all  | `GetAll </en/latest/database/getAll.html>`_     | PGetAll             | CacheGetAll   | CachePGetAll              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| assoc| `GetAssoc </en/latest/database/getASSOC.html>`_ | PGetAssoc           | CacheGetAssoc | CachePGetAssoc            |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| col  | `GetCol </en/latest/database/getCol.html>`_     | PGetCol             | CacheGetCol   | CachePGetCol              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| one  | `GetOne </en/latest/database/getOne.html>`_     | PGetOne             | CacheGetOne   | CachePGetOne              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| row  | `GetRow </en/latest/database/getRow.html>`_     | PGetRow             | CacheGetRow   | CachePGetRow              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+

.. seealso::

   `Prepared statements </en/latest/prepared_statements.html>`_ & `PExecute </en/latest/database/PExecute.html>`_.


Examples
........


.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->Execute("INSERT INTO table (name,email,age) VALUES('name', 'email', 70)");


You can also catch exception and continue execution:

.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   try {
       $db->Execute('CREATE TABLE myCity LIKE City');
   } catch (Exception $e) {
       echo "Table already exists.",$db->isCli(1);
   }

   $db->Execute("INSERT INTO myCity VALUES (NULL, 'Toluca', 'MEX', 'México', 467713)");