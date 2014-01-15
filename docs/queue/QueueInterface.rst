QueueInterface
==============

``QueueInterface`` is **DALMP** interface to be use with the `DALMP\\Queue </en/latest/queue.html>`_ class.


The common methods are:

=========================== =======================================
Method                      Description
=========================== =======================================
enqueue($key)               Adds an element to the queue.
dequeue($limit = false)     Dequeues an element from the queue.
delete($key)                Delete an element from the queue.
X()                         Return the queue object.
=========================== =======================================


All the queue backends must implement this `interface <https://github.com/nbari/DALMP/blob/master/src/DALMP/Queue/QueueInterface.php>`_ in order to properly work with **DALMP**.

__construct
...........

The construct for each queue backend maybe be different and it is used for
defining specific options like the host, port, path etc,

.. seealso::

   `PHP Object Interfaces <http://www.php.net/manual/en/language.oop5.interfaces.php>`_.
