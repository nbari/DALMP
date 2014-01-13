CompleteTrans
=============

Complete the transaction, this must be used in conjunction with method
`StartTrans </en/latest/database/StartTrans.html>`_.

If success returns **true**, otherwise **false**.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 29, 30, 31, 33

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->Execute('CREATE TABLE IF NOT EXISTS t_test (id INT NOT NULL PRIMARY KEY) ENGINE=InnoDB');
   $db->Execute('TRUNCATE TABLE t_test');
   $db->FetchMode('ASSOC');

   $db->StartTrans();
   $db->Execute('INSERT INTO t_test VALUES(1)');
       $db->StartTrans();
       $db->Execute('INSERT INTO t_test VALUES(2)');
       print_r($db->GetAll('SELECT * FROM t_test'));
           $db->StartTrans();
           $db->Execute('INSERT INTO t_test VALUES(3)');
           print_r($db->GetAll('SELECT * FROM t_test'));
               $db->StartTrans();
               $db->Execute('INSERT INTO t_test VALUES(7)');
               print_r($db->GetALL('SELECT * FROM t_test'));
           $db->RollBackTrans();
           print_r($db->GetALL('SELECT * FROM t_test'));
           $db->CompleteTrans();
       $db->CompleteTrans();
   $db->CompleteTrans();

   if ($db->CompleteTrans()) {
    // your code
   }


.. seealso::

   `StartTrans </en/latest/database/StartTrans.html>`_