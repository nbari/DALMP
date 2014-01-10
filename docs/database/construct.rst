__construct
===========


``DALMP\\Database`` takes the parameters from a `DNS <http://en.wikipedia.org/wiki/Data_source_name>`_ (database source name) so
before you can start using it you need to define this values.

DSN format
..........

.. code-block:: rest

   charset://username:password@host:port/database

When using `Unix domain sockets <http://en.wikipedia.org/wiki/Unix_domain_socket>`_::

   charset://username:password@unix_socket=\path\of\the.socket/database

* Notice that the path of the socket is using backslashes.

::

    \path\of\the.socket

Will be translated to::

    /path/of/the.socket


.. seealso::

   `Unix sockets vs Internet sockets <http://lists.freebsd.org/pipermail/freebsd-performance/2005-February/001143.html>`_



Examples
........

.. sidebar:: DSN values

   :charset: utf8
   :user: $user
   :password: $password
   :host: 127.0.0.1
   :database:  test

.. code-block:: php
   :linenos:
   :emphasize-lines: 8

   <?php

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   require_once 'dalmp.php';

   $DSN = "utf8://$user:$password@127.0.0.1/test";

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
    */
   $db->debug(1);

   echo $db, PHP_EOL; // print connection details

If you wan to use the system default charset the DSN would be:

.. code-block:: php
   :linenos:

   $DSN = "mysql://$user:$password@127.0.0.1/test";

* notice the **mysql://** instead of the **utf8://**


.. seemore::

   `Quickstart </en/latest/Quickstart.html>`_.