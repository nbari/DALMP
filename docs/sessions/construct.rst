__construct
===========

In order to use the ``DALMP\Sessions`` you need to create an instance of it,
while creating the instance you define the backend that will store the sessions
and the hash algorithm used to create them.

Parameters
..........

::

    __construct($handler = false, $algo = 'sha256')

:$handler: If false uses `SQLite /en/latest/sessions/SQLite.html>`_, otherwise argument must be an instance of `SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.
:$algo: Allows you to specify the `hash algorithm <http://pt1.php.net/manual/en/function.hash-algos.php>`_ used to generate the session IDs - default **sha256**.

The current backends are:

* `Files </en/latest/sessions/Files.html>`_.
* `Memcache </en/latest/sessions/Memcache.html>`_.
* `MySQL </en/latest/sessions/MySQL.html>`_.
* `Redis </en/latest/sessions/Redis.html>`_.
* `SQLite </en/latest/sessions/SQLite.html>`_.


Constants
.........

::

    define('DALMP_SESSIONS_MAXLIFETIME', 900);

If set, the value is used as an argument for the `session.gc_maxlifetime <http://www.php.net/manual/en/session.configuration.php#ini.session.gc-maxlifetime>`_ with specifies the number of seconds after which data will be seen as
'garbage' and potentially cleaned up.

::

    define('DALMP_SESSIONS_REF', 'UID');

The global reference value that will be checked/used when handling sessions,
every session will contain this value.

::

    define('DALMP_SESSIONS_KEY', '4d37a965ef035a7def3cd9c1baf82924c3cc792a');

A unique key that will be used to create the store the session on
Memcache/Redis backends, this is useful when working in shared hosted
enviroments, basically to avoid collitions.
