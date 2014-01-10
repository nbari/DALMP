AutoExecute
===========

Automatically prepares and runs an INSERT or UPDATE query based on variables
you supply.

This is very usefull when you simple want to save post data from a huge web
form, AutoExecute will genereate a mysql prepared statement from the array used
and INSERT or UPDATE

Parameters
..........

::

    AutoExecute($table, $fields, $mode = 'INSERT', $where = null)

:$table: The name of the table you want to INSERT or UPDATE

:$fields: An assoc array (key => value), keys are fields names, values are values of these fields.

:$mode: INSERT or UPDATE

:$where: A string to be used in the WHERE clause. This is only used when $mode = UPDATE.


Examples
........


Insert all `$_POST <http://www.php.net/manual/en/reserved.variables.post.php>`_ data example:

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@unix_socket=\tmp\mysql.sock/test';

   $db = new DALMP\Database($DSN);

   $db->AutoExecute('mytable',$_POST);
   // the key values of $_POST must be equal to the column names of the mysql table


Update example:

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@unix_socket=\tmp\mysql.sock/test';

   $db = new DALMP\Database($DSN);

   $date = array('username' => 'nbari',
                 'status' => 1);

   $db->AutoExecute('mytable',$data, 'UPDATE','status=0 AND uid=14');