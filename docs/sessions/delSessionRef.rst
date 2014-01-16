delSessionRef
=============

Delete sessions containing a specific reference.

Parameters
..........

    delSessionRef($ref)

:$ref: Value of the reference to search for.


Example
.......

In this example all the sessions containing the value '3', will be deleted.

.. code-block:: php
   :linenos:
   :emphasize-lines: 11

   <?php

   require_once 'dalmp.php';

   $cache= new DALMP\Cache\Memcache('127.0.0.1', 11211, 1, 1);

   $handler = new DALMP\Sessions\Memcache($cache, 'ID');

   $sessions = new DALMP\Sessions($handler, 'sha512);

   $sessions->delSessionRef('3');
