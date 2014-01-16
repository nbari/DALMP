Memcache
========

Handler for storing sessions in memcache, implements
`SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.


__construct
...........

::

    __construct(\DALMP\Cache\Memcache $cache, $sessions_ref = 'UID')

:$cache: An instance of `DALMP\Cache\Memcache </en/latest/cache/memcache.html>`_.
:$sessions_ref: Name of the global reference, defaults to **UID**.


Constants
.........

::

    define('DALMP_SESSIONS_REF', 'UID');

The global reference value that will be checked/used when handling sessions,
every session will contain this value.

::

    define('DALMP_SESSIONS_KEY', '4d37a965ef035a7def3cd9c1baf82924c3cc792a');

A unique key that will be used to create the store the session on
Memcache/Redis backends, this is useful when working in shared hosted
enviroments, basically to avoid collisions.
