CacheInterface
==============

``CacheInterface`` is **DALMP** interface to be use with the `DALMP\\Cache </en/latest/cache.html>`_ class.


The common methods are:

=========================== =======================================
Method                      Description
=========================== =======================================
Delete($key)                Delete item from the server.
Flush()                     Flush all existing items at the server.
Get($key)                   Retrieve item from the server.
Set($key, $value, $ttl = 0) Store data at the server.
Stats()                     Get statistics of the server.
X()                         Return the cache object.
=========================== =======================================


All the cache backends must implement this `interface <https://github.com/nbari/DALMP/blob/master/src/DALMP/Cache/CacheInterface.php>`_ in order to properly work with **DALMP**.

__construct
...........

The construct for each cache backend maybe be different and it is used for
defining specific options like the host, port, path etc,

.. seealso::

   `PHP Object Interfaces <http://www.php.net/manual/en/language.oop5.interfaces.php>`_.
