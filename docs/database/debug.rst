debug
=====

This method will enable debugging, so that you can trace your full queries.

Parameters
..........

::

    debug($log2file = false, $debugFile = false)


:$log2file: When set to **1**, log is written to a single file, if **2** it creates multiple log files per request so that you can do a more intense debugging, 'off' stop debuging.

:$debugFile: Path of the file to write logs, defaults to ``/tmp/dalmp.log``


Constants
.........

::

   define('DALMP_DEBUG_FILE', '/home/tmp/debug.log');

Defines where the debug file will be write to.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 13

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   /**
    * 1 log to single file
    * 2 log to multiple files (creates a log per request)
    * off stop debug
    */
    $db->debug(1);

    try {
      $rs = $db->getOne('SELECT now()');
    } catch (Exception $e) {
      print_r($e->getMessage());
    }

    echo $db,PHP_EOL; // print connection details