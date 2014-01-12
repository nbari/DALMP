csv
===


This method exports your results in CSV
(`http://en.wikipedia.org/wiki/Comma-separated_values <http://en.wikipedia.org/wiki/Comma-separated_values>`_).


Parameters
..........

::

    csv($sql)

:$sql: Your normal sql or either a prepared statement query.

:returns: CVS

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   header("Content-type: application/csv");
   header("Content-Disposition: attachment; filename=$filename.csv");
   header("Pragma: no-cache");
   header("Expires: 0");

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->csv('SELECT row1, row2, row3, row4 FROM table WHERE uid=? AND cat=?', 3, 'oi');