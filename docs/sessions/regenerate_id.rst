regenerate_id
=============

The ``regenerate_id`` method, regenerate a sessions and create a fingerprint,
helps to prevent HTTP session hijacking attacks.

Parameters
..........

::

    regenerate_id($use_IP = true)

:$use_IP: Include client IP address on the fingerprint.


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

   $_SESSION['test'] = 1 + @$_SESSION['test'];

   echo $_SESSION['test'];

   echo session_id();

.. seealso::

   `PHP session_regenerate_id <http://www.php.net/session_regenerate_id>`_.
