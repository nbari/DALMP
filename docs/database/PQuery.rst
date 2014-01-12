Pquery
======

Prepared Statements query.

Parameters
..........

::

    Pquery($out = array())

:$out: An empty array that will contain the output.


This method is useful, in cases where you need to process each row of a query
without consuming too much memory.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 14,15

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->PExecute('SELECT * FROM Country WHERE Continent = ?', 'Europe');

   $out = array();
   while ($rows = $db->Pquery($out)) {
       print_r($out);
   }