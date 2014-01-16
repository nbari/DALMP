getSessionsRefs
===============

Return array of sessions containing any reference.

Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 9

   <?php

   require_once 'dalmp.php';

   $cache= new DALMP\Cache\Memcache('127.0.0.1', 11211, 1, 1);

   $handler = new DALMP\Sessions\Memcache($cache, 'ID');

   $sessions = new DALMP\Sessions($handler, 'sha512);

   $sessions->getSessionsRefs();
