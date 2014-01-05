Quick Start
===========


**DALMP** takes the parameters from a `DSN <http://en.wikipedia.org/wiki/Data_source_name>`_ (database source name) so before you can
start using it you need to define these values as shown in the self explanatory
example below.

.. code-block:: php
   :linenos:

   <?php

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   require_once 'dalmp.php';

   /**
    * example of a simple connection
    *
    * charset: utf8
    * user: dalmp
    * password: password
    * host: 127.0.0.1
    * database: dalmptest
    *
    */
   $DSN = "utf8://$user:$password@127.0.0.1/dalmptest";

   $db = new DALMP\Database($DSN);

   try {
       $rs = $db->getOne('SELECT now()');
   } catch (\Exception $e) {
       print_r($e->getMessage());
   }

   /**
    * 1 log to single file
    * 2 log to multiple files (creates a log per request)
    * 'off' to stop debuging
    \*/
   $db->debug(1);

   echo $db, PHP_EOL; // print connection details


If you wan to use the system default charset the DSN would be:

.. code-block:: php
   :linenos:

   $DSN = "mysql://$user:$password@127.0.0.1/dalmptest";

If you want to use SSL, an array containing the SSL parameters must be passed as
the second argument to the database method example:

.. code-block:: php
   :linenos:

   $ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);

   $db = new DALMP\Database('latin1://root:mysql@127.0.0.1/dalmp', $ssl);

In this case the DSN is formed by:

:charset: latin1
:user: root
:password: mysql
:host: 127.0.0.1
:database:  dalmp
