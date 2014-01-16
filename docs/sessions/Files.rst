Files
=====

Handler for storing sessions in local hard disk, implements
`SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.


__construct
...........

::

    __construct($sessions_dir = false)

:$sessions_dir: Path to store the sessions, default '/tmp/dalmp_sessions'.


Constants
.........

::

    define('DALMP_SESSIONS_DIR', '/tmp/my_sessions');


If set and no ``$session_dir`` defined while initializating the class, it will
use this value.
