regenerate_id
=============

The ``regenerate_id`` methods, regenerate a sessions and create a fingerprint,
helps to prevent HTTP session hijacking attacks.

Parameters
..........

::

    regenerate_id($use_IP = true)

:$use_IP: Include client IP address on the fingerprint.


.. seealso::

   `PHP session_regenerate_id <http://www.php.net/session_regenerate_id>`_.


Example
.......


.. code-block:: php
   :linenos:
   :emphasize-lines: 8

   <?php

   require_once 'dalmp.php';

   $sessions = new DALMP\Sessions();

   if ((mt_rand() % 10) == 0) {
       $sessions->regenerate_id(true);
   }
