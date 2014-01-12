ErrorNum
========

Returns the error code for the most recent function call.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 16

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   try {
     $db->Execute('SET a=1');
   } catch (Exception $d) {
     // Errormessage: 1193
     printf("Errormessage: %s\n", $db->ErrorNum());
   }