multipleInsert
==============

Performs one query to insert multiple records.


Parameters
..........

::

    multipleInsert($table, array $col_name, array $values)


:$table: Name of the table to insert the data.
:$col_name: Array containing the name of the columns.
:$values: Multidimensional Array containing the values.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 20

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $values = array(
       array(1,2,3),
       array(1,3),
       array('date','select', 3),
       array('niño','coraçao', 'Ú'),
       array(null,5,7)
   );

   $rs = $db->multipleInsert('tests', array('col1', 'col2', 'col3'), $values);


.. note::

   The ``multipleInsert`` method uses `Prepared statements PExecute </en/latest/database/PExecute.html>`_ to Insert the data.