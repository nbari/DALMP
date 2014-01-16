SQLite
======

Handler for storing sessions in SQLite, implements
`SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.


__construct
...........

::

    __construct($filename = false, $sessions_ref = 'UID', $enc_key = false)

:$filename: Path to the SQLite database, or :memory: to use in-memory database.
:$sessions_ref: Name of the global reference, defaults to **UID**.
:$enc_key: The encryption key, default not set.

.. seealso::

   For using sqlite3 databases encrypted you need to install
   sqlcipher: `sqlcipher.net <http://sqlcipher.net/>`_.
