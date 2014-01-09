Prepared statements
===================

`Prepared statements <http://en.wikipedia.org/wiki/Prepared_statement>`_ help
you in many cases to avoid avoid mysql injections and helps increasing security
of your queries by separating the SQL logic from the data being supplied.

`DALMP\\Database </en/latest/database.html>`_ by default tries to determine the
type of the data supplied, so you can just focus on your query without needing
to specify the type of data, If you preffer you can manually specify the type of
the data. The following table, show the characters which specify the types for
the corresponding bind variables:


+-----------+--------------------------------------------------------------+
| Character | Description                                                  |
+===========+==============================================================+
| i         | corresponding variable has type integer                      |
+-----------+--------------------------------------------------------------+
| d         | corresponding variable has type double                       |
+-----------+--------------------------------------------------------------+
| s         | corresponding variable has type string                       |
+-----------+--------------------------------------------------------------+
| b         | corresponding variable is a blob and will be sent in packets |
+-----------+--------------------------------------------------------------+


.. seealso::

   Method `prepare </en/latest/database/Prepare.html>`_, & `mysqli_stmt_bind_param <http://www.php.net/manual/en/mysqli-stmt.bind-param.php>`_

To use "Prepared statements" on your SQL statements you can use the following
methods:


+------+----------+--------------------------------------------------+---------------+---------------------------+
| Name | Normal   | Prepared statements                              | Cache Normal  | Cache Prepared statements |
+======+==========+==================================================+===============+===========================+
| all  | GetAll   | `PGetAll </en/latest/database/getAll.html>`_     | CacheGetAll   | CachePGetAll              |
+------+----------+--------------------------------------------------+---------------+---------------------------+
| assoc| GetAssoc | `PGetAssoc </en/latest/database/getASSOC.html>`_ | CacheGetAssoc | CachePGetAssoc            |
+------+----------+--------------------------------------------------+---------------+---------------------------+
| col  | GetCol   | `PGetCol </en/latest/database/getCol.html>`_     | CacheGetCol   | CachePGetCol              |
+------+----------+--------------------------------------------------+---------------+---------------------------+
| one  | GetOne   | `PGetOne </en/latest/database/getOne.html>`_     | PGetOne       | CacheGetOne               |
+------+----------+--------------------------------------------------+---------------+---------------------------+
| row  | GetRow   | `PGetRow </en/latest/database/getRow.html>`_     | PGetRow       | CacheGetRow               |
+------+----------+--------------------------------------------------+---------------+---------------------------+

.. note::

   Notice that when using "Prepared statements" basically the methods are
   prefixed with a **P**

.. seealso::

   Method `Cache </en/latest/database/Cache.html>`_


Examples
........

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   require_once 'dalmp.php';

   $DSN = "utf8://$user:$password@127.0.0.1/test";

   $db = new DALMP\Database($DSN);

   $db->PExecute('SET time_zone=?', 'UTC');


Example using the `LIKE <http://dev.mysql.com/doc/refman/5.0/en/pattern-matching.html>`_ statement:

.. code-block:: php
   :linenos:

   <?php

   $sql = 'SELECT Name, Continent FROM Country WHERE Population > ? AND Code LIKE ?';

   $rs = $db->FetchMode('ASSOC')->PGetAll($sql, 1000000, '%P%');


If you want to define the types, you must pass an array specifying each type.
Example:

.. code-block:: php
   :linenos:

   <?php

   $sql = 'SELECT * FROM mytable WHERE name=? AND id=?';

   $rs = $db->FetchMode('ASSOC')->PGetAll($sql, array('s' => '99.3', 7));

An Insert example:

.. code-block:: php
   :linenos:

   <?php

   $db->PExecute('INSERT INTO mytable (colA, colB) VALUES(?, ?)', rand(), rand());

.. seealso::

   Method `PExecute </en/latest/database/PExecute.html>`_

An Update example:

.. code-block:: php
   :linenos:

   <?php

   $db->PExecute('UPDATE Country SET code=? WHERE Code=?', 'PRT', 'PR');

.. warning::

   When updating the return value **0**, Zero indicates that no records where
   updated.