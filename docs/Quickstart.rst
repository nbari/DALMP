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


   $db = new DALMP\Database("utf8://$user:$password@127.0.0.1/dalmptest");
   try {
       $rs = $db->getOne('SELECT now()');
   } catch (\Exception $e) {
       print_r($e->getMessage());
   }

   /**
    * 1 log to single file
    * 2 log to multiple files (creates a log per request)
    * 'off' to stop debuging
    */
   $db->debug(1);

   echo $db, PHP_EOL; // print connection details
