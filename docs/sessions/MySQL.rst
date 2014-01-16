MySQL
=====

Handler for storing sessions in MySQL, implements
`SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.


__construct
...........

::

    __construct(\DALMP\Database $DB, $sessions_ref = 'UID')

:$DB: An instance of `DALMP\Database </en/latest/cache/database.html>`_.
:$sessions_ref: Name of the global reference, defaults to **UID**.


Constants
.........

::

    define('DALMP_SESSIONS_REF', 'UID');

The global reference value that will be checked/used when handling sessions,
every session will contain this value.

::

    define('DALMP_SESSIONS_TABLE', 'dalmp_sessions');

Name of the MySQL table where sessions will be stored, by default the table
'dalmp_sessions' will be used.


MySQL table schema
..................

For storing PHP sessions on mysql you need to create a table with the following
schema:

.. code-block:: sql
   :linenos:

   CREATE TABLE IF NOT EXISTS `dalmp_sessions` (
   `sid` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
   `expiry` int(11) unsigned NOT NULL DEFAULT '0',
   `data` longtext CHARACTER SET utf8 COLLATE utf8_bin,
   `ref` varchar(255) DEFAULT NULL,
   `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   PRIMARY KEY (`sid`),
   KEY `index` (`ref`,`sid`,`expiry`)
   ) DEFAULT CHARSET=utf8;


.. seealso::

   `DALMP Quickstart </en/latest/Quickstart.html>`_.
