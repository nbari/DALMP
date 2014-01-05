Quick Start
===========


**DALMP** takes the parameters from a `DSN <http://en.wikipedia.org/wiki/Data_source_name>`_ (database source name) so before you can start using it you need to define these values:


DSN format
..........

.. code-block:: rest

   charset://username:password@host:port/database

When using unix sockets::

   charset://username:password@unix_socket=\path\of\the.socket/database

* Notice that the path of the socket is using backslashes.

::

    \path\of\the.socket

Will be translated to::

    /path/of/the.socket


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

If you want to use `SSL <http://en.wikipedia.org/wiki/Secure_Sockets_Layer>`_, an array containing the SSL parameters must be passed as the second argument to the database method example:

.. sidebar:: DSN values

   :charset: latin1
   :user: root
   :password: secret
   :host: 127.0.0.1
   :database: test

.. code-block:: php
   :linenos:
   :emphasize-lines: 1, 3

   $ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);

   $DSN = 'latin1://root:secret@127.0.0.1/test';

   $db = new DALMP\Database($DSN, $ssl);


The SSL array argument, must follow this format:

:key: The path name to the key file.
:cert: The path name to the certificate file.
:ca: The path name to the certificate authority file.
:capath: The pathname to a directory that contains trusted SSL CA certificates in PEM format.
:cipher:  A list of allowable ciphers to use for SSL encryption.


.. note::
   When using SSL, `OpenSSL <http://www.php.net/openssl>`_ support must be enable for this to work.
