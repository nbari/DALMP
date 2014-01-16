__construct
===========

In order to use the ``DALMP\Sessions`` you need to create an instance of it,
while creating the instance you define the backend that will store the sessions
and the hash algorithm used to create them.

Parameters
..........

::

    __construct($handler = false, $algo = 'sha256')

:$handler: If false uses SQLite, otherwise argument must be an instance of `SessionHandlerInterface <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.
:$algo: Allows you to specify the `hash algorithm <http://pt1.php.net/manual/en/function.hash-algos.php>`_ used to generate the session IDs - default **sha256**.
