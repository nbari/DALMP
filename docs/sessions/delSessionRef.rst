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
   :emphasize-lines: 9

   <?php

   require_once 'dalmp.php';

   $cache= new DALMP\Cache\Memcache('127.0.0.1', 11211, 1, 1);

   $sessions = new DALMP\Sessions($cache, 'UID');

   $sessions->delSessionRef('3');
