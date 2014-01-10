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
**P** `GetAll </en/latest/database/getAll.html>`_      | Executes the SQL and returns the all the rows as a 2-dimensional array.
                                                       | If an error occurs, false is returned.
**P** `GetRow </en/latest/database/getRow.html>`_      | Executes the SQL and returns the first row as an array.
                                                       | If an error occurs, false is returned.
**P** `GetCol </en/latest/database/getCol.html>`_      | Executes the SQL and returns all elements of the first column as a 1-dimensional array.
                                                       | If an error occurs, false is returned.
**P** `GetOne </en/latest/database/getOne.html>`_      | Executes the SQL and returns the first field of the first row.
                                                       | If an error occurs, false is returned.
**P** `GetASSOC </en/latest/database/getASSOC.html>`_  | Executes the SQL and returns an associative array for the given query.
                                                       | If the number of columns returned is greater to two, a 2-dimensional array is returned with the first column of the recordset becomes the keys to the rest of the rows.
                                                       | If the columns is equal to two, a 1-dimensional array is created, where the the keys
                                                       | directly map to the values.
                                                       | If an error occurs, false is returned.
====================================================== =========================================================================================

.. note::

  Notice that when using "Prepared statements" the methods are
  prefixed with a **P**.


Examples
........