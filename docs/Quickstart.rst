Quick Start
===========

.. code-block:: php
   :linenos:

   <?php

   require_once 'dalmp.php';

   // DSN format: charset://username:password@host:port/database
   $db = new DALMP('utf8://root:'.rawurlencode('pass-?/:word').'@127.0.0.1:3306/mydatabase');

   $rs = $db->FetchMode('ASSOC')->PGetAll('SELECT id, name FROM users WHERE id=?',3);

**DALMP** takes the parameters from a `DSN <http://en.wikipedia.org/wiki/Data_source_name>`_ (database source name).


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


SSL
...


If you want to use `SSL <http://en.wikipedia.org/wiki/Secure_Sockets_Layer>`_, an array containing the SSL parameters must be passed as the second argument to the database method example:

.. code-block:: php
   :linenos:
   :emphasize-lines: 1, 3

   $ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);

   $DSN = 'latin1://root:secret@127.0.0.1/test';

   $db = new DALMP\Database($DSN, $ssl);


The **$ssl** array argument, must follow this format:

:key: The path name to the key file.
:cert: The path name to the certificate file.
:ca: The path name to the certificate authority file.
:capath: The pathname to a directory that contains trusted SSL CA certificates in PEM format.
:cipher:  A list of allowable ciphers to use for SSL encryption.


.. note::
   When using SSL, PHP `OpenSSL <http://www.php.net/openssl>`_ support must be enable for this to work.


To check that your connection has SSL you can test with this:

.. code-block:: php
   :linenos:

   <?php

   require_once 'dalmp.php';

   $ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);

   $DSN = 'utf8://root:secret@127.0.0.1/test';

   $db = new DALMP\Database($DSN, $ssl);

   try {
     $db->getOne('SELECT NOW()');
     print_r($db->FetchMode('ASSOC')->GetRow("show variables like 'have_ssl'"));
   } catch (\Exception $e) {
     print_r($e->getMessage());
   }

   try {
     print_r($db->GetRow("show status like 'ssl_cipher'"));
   } catch (\Exception $e) {
     print_r($e->getMessage());
   }


If you have SSL you will get something like:

.. code-block:: php
   :linenos:
   :emphasize-lines: 4,10

   Array
   (
     [Variable_name] => have_ssl
     [Value] => YES
   )

   Array
   (
     [Variable_name] => Ssl_cipher
     [Value] => DHE-RSA-AES256-SHA
   )

Otherwise:

.. code-block:: php
   :linenos:
   :emphasize-lines: 4, 10

   Array
   (
     [Variable_name] => have_ssl
     [Value] => DISABLED
   )

   Array
   (
     [Variable_name] => Ssl_cipher
     [Value] =>
   )

Example using a socket
......................

.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

   require_once 'dalmp.php';

   $DSN = "utf8://$user:$password".'@unix_socket=\tmp\mysql.sock/test';

   $db = new DALMP\Database($DSN);

   $db->debug(1);

   try {
     echo PHP_EOL, 'example using unix_socket: ', $db->getOne('SELECT NOW()'), PHP_EOL;
   } catch (\Exception $e) {
     print_r($e->getMessage());
   }

   echo $db;
   # will print: DALMP :: connected to: db, Character set: utf8, Localhost via UNIX socket,...


.. seealso::

   `DALMP Examples <https://github.com/nbari/DALMP/tree/master/examples>`_
