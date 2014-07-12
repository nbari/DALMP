X
=

Returns the `mysqli <http://www.php.net/manual/en/class.mysqli.php>`_ object.

Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 12,13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->X()->ping();
   $db->X()->stat();

   $rs = $db->GetOne('SELECT DATABASE()');
   echo $rs, PHP_EOL;

   $db->X()->select_db('mysql');

   $rs = $db->GetOne('SELECT DATABASE()');
   echo $rs, PHP_EOL;



.. seealso::

   `class.mysqli.php <http://www.php.net/manual/en/class.mysqli.php>`_, `ping <http://www.php.net/manual/en/mysqli.ping.php>`_, `stat <http://www.php.net/manual/en/mysqli.stat.php>`_.