PExecute
========

Execute an SQL statement using `Prepared Statements </en/latest/prepared_statements.html>`_.

Parameters
..........

::

    PExecute($sql, $varN)

:$sql: The MySQL query to perform on the database
:$varN: The variables that will be placed instead of the **?** placeholder separated by a ',' or it can be the method `Prepare </en/latest/database/Prepare.html>`_.


.. seealso::

  `SQL syntax prepared statements <http://dev.mysql.com/doc/refman/5.0/en/sql-syntax-prepared-statements.html>`_.