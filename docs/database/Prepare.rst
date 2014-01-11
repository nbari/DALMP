Prepare
=======

Prepare arguments for the Prepared Statements `PExecute </en/latest/database/PExecute.html>`_ method.

Parameters
..........

::

    Prepare($arg= null)

:$arg: Argument to be used in the PExecute method, if no input it will return the array with the prepared statements.

The prepare method automatically detect the input type, you can also override
this, using something like::

    Prepare('s','1e1')

Example
.......

Building dynamic queries that require prepared statements:

.. code-block:: php
   :linenos:
   :emphasize-lines: 4

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $x = 3;
   $id = 1;

   $db->Prepare($id)

   $sql = 'SELECT * FROM test WHERE id=?';

   if ($x == 3) {
       $db->Prepare($x);
       $sql .= 'AND id !=?';
   }

   $db->Prepare('s', 'colb');

   $sql .= 'AND colB=?';

   /**
    * this will produce a query like:
    * "sql: SELECT * FROM test WHERE id=? AND id !=? AND colB=?" with params = ["iis",1,3,"colb"]
    */
    $rs = $db->PgetAll($sql, $db->Prepare());


.. seealso ::

   `Prepared statements </en/latest/prepared_statements.html>`_