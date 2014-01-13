PExecute
========

Execute an SQL statement using `Prepared Statements </en/latest/prepared_statements.html>`_.

Parameters
..........

::

    PExecute($sql, $varN)

:$sql: The MySQL query to perform on the database
:$varN: The variable(s) that will be placed instead of the **?** placeholder separated by a ',' or it can be the method `Prepare </en/latest/database/Prepare.html>`_.


.. seealso::

  `SQL syntax prepared statements <http://dev.mysql.com/doc/refman/5.0/en/sql-syntax-prepared-statements.html>`_.


Like the `Execute </en/latest/database/Execute.html>`_ Method, in most cases you
probably only use this method when Inserting or Updating data for retrieving
data you can use:

====================================================== =========================================================================================
method                                                 Description
====================================================== =========================================================================================
**P**\ `getAll </en/latest/database/getAll.html>`_     Executes the SQL and returns the all the rows as a 2-dimensional array.
**P** `getRow </en/latest/database/getRow.html>`_      Executes the SQL and returns the first row as an array.
**P** `getCol </en/latest/database/getCol.html>`_      Executes the SQL and returns all elements of the first column as a 1-dimensional array.
**P** `getOne </en/latest/database/getOne.html>`_      Executes the SQL and returns the first field of the first row. -
**P** `getASSOC </en/latest/database/getASSOC.html>`_  Executes the SQL and returns an associative array for the given query. \
                                                       If the number of columns returned is greater to two, a 2-dimensional array is returned\
                                                       with the first column of the recordset becomes the keys to the rest of the rows. \
                                                       If the columns is equal to two, a 1-dimensional array is created, where the the keys \
                                                       directly map to the values.
====================================================== =========================================================================================

.. note::

  Notice that when using "Prepared statements" the methods are
  prefixed with a **P**.


Examples
........

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password@127.0.0.1/test";

   $db = new DALMP\Database($DSN);

   $db->PExecute('SET time_zone=?', 'UTC');


An Insert example:

.. code-block:: php
   :linenos:

   <?php

   $db->PExecute('INSERT INTO mytable (colA, colB) VALUES(?, ?)', rand(), rand());


An Update example:

.. code-block:: php
   :linenos:

   <?php

   $db->PExecute('UPDATE Country SET code=? WHERE Code=?', 'PRT', 'PRT');

.. warning::

   When updating the return value **0**, Zero indicates that no records where
   updated.