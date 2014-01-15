SQLite
======

Implements the ``QueueInteface`` using as `SQLite <http://www.sqlite.org>`_ as the queue backend.

Requires `PHP SQLite3 support <http://www.php.net/manual/en/book.sqlite3.php>`_

__construct
...........

::

  __construct($filename, $queue_name, $enc_key)

:$filename: Path to the SQLite database, or :memory: to use in-memory database.
:$queue_name:  Name of the queue, defaults to 'default'.
:$enc_key: The encryption key, default not set.


.. seealso::

   For using sqlite3 databases encrypted you need to install
   sqlcipher: `sqlcipher.net <http://sqlcipher.net/>`_.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 5

   <?php

    require_once 'dalmp.php';

    $queue = new DALMP\Queue(new DALMP\Queue\SQLite('/tmp/dalmp_queue.db'));

    echo 'enqueue status: ', var_dump($queue->enqueue('this is a teste'));

    echo 'dequeue all: ', print_r($queue->dequeue(), true);

    echo 'dequeue only 3: ', print_r($queue->dequeue(3), true);

    echo 'delete from queue: ', var_dump($queue->delete(63));


.. seealso::

   `Queue Examples <https://github.com/nbari/DALMP/tree/master/examples/queue>`_.
