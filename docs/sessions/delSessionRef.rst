delSessionRef
=============

Delete sessions containing a specific reference.

Parameters
..........

    delSessionRef($ref)

:$ref: Name of Item of to be deleted from the reference.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 9

   <?php

   require_once 'dalmp.php';

   $cache= new DALMP\Cache\Memcache('127.0.0.1', 11211, 1, 1);

   $sessions = new DALMP\Sessions($cache, 'UID');

   $sessions->delSessionRef('3');
