qstr
====

Quotes a string, used when not using prepared statements and want to safetly
insert/update data, it uses `real_escape_string <http://www.php.net/mysqli_real_escape_string>`_.

Parameters
..........

::

    qstr($string)

:$string: The var to quote


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $data = "nbari' OR admin";
   $query = $db->qstr($data);

   $db->GetRow("SELECT * FROM users WHERE name=$query");