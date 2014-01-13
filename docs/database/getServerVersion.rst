getServerVersion
================

Returns server version number as an integer.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 15, 20

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->FetchMode('ASSOC')->PGetRow('SELECT * FROM Country WHERE Region = ? LIMIT 1', 'Caribbean');

   /**
    * After a query made you can get the server version
    */
   echo $db->getServerVersion();