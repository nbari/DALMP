ErrorMsg
========

Returns a string description of the last error.

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
     // Errormessage: Unknown system variable 'a'
     printf("Errormessage: %s\n", $db->ErrorMsg());
   }