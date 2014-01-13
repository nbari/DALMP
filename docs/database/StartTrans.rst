StartTrans
==========

Start the transaction, for this the mysql table must be of type `InnoDB <http://en.wikipedia.org/wiki/InnoDB>`_.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 16, 18, 21, 24

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

   `CompleteTrans </en/latest/database/CompleteTrans.html>`_, `MySQL START
   TRANSACTION, COMMIT and ROLLBACK Syntax <http://dev.mysql.com/doc/refman/5.5/en/commit.html>`_.