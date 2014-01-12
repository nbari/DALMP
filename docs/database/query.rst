Query
=====

Fetch a result row as an associative array, a numeric array, or both.


.. seealso::

   `FetchMode </en/latest/database/FetchMode.html>`_


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 15

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->Execute('SELECT * FROM City');

   if ($rs) {
       while (($rows = $db->query()) != false){
           list($r1,$r2,$r3) = $rows;
           echo "w1: $r1, w2: $r2, w3: $r3",$db->isCli(1);
       }
   }

.. note::

   Use `Pquery </en/latest/database/PQuery.html>`_ when using  `Prepared statements </en/latest/prepared_statements.html>`_.