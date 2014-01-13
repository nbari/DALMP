getClientVersion
================


Returns client version number as an integer.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   echo $db->getClientVersion();