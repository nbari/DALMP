Insert_Id
=========

Returns the auto generated id used in the last query.

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
       $db->Execute('CREATE TABLE myCity LIKE City');
   } catch (Exception $e) {
      echo "Table already exists.",$db->isCli(1);
   }

   $db->Execute("INSERT INTO myCity VALUES (NULL, 'Toluca', 'MEX', 'México', 467713)");
   printf ("New Record has id %d.\n", $db->Insert_id());