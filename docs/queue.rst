DALMP\\Queue
============


The ``DALMP\Queue`` class works as a dispatcher for the current Queue classes, following  a common interface in order to maintain
compatibility with other **DALMP** classes.

*Object interfaces allow you to create code which specifies which methods a class must implement, without having to define how these methods are handled.*

.. seealso::

   `PHP Object Interfaces <http://www.php.net/manual/en/language.oop5.interfaces.php>`_.


**Parameters**

::

  DALMP\Queue(object)

:object: An `QueueInterface instance </en/latest/cache/CacheInterface.html>`_.

**Why?**

There are times where database go down or you can't Insert/Update data into a
table because of the 'too many connections mysql'. In this cases a queue always
is useful so that you can later process the queries and not lose important data.

**Example**

.. code-block:: php
   :linenos:
   :emphasize-lines: 12, 23

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@localhost/test';

   $db = new DALMP\Database($DSN);

   $queue = new DALMP\Queue(new DALMP\Queue\SQLite('/tmp/dalmp_queue.db'));

   /**
    * In case something goes wrong, the database is unavailable, fields missing,  etc,
    * you can save the 'sql query' and later process it again.
    */

    $sql = "INSERT INTO testX SET colA=(NOW())";
    try {
        $rs = $db->Execute($sql);}
    } catch(Exception $e) {
       $queue->enqueue($sql);
    }



**See also**

.. toctree::
   :maxdepth: 2

   queue/QueueInterface
   queue/SQLite

.. note::

   The **Dalmp\\Queue** has no dependency with the `DALMP\\Database </en/latest/database.html>`_ class, this means that you can use only the Database or the Queue classes with out need to depend on eitherone.
