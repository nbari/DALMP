DALMP
=====

Database Abstraction Layer for MySQL using PHP
----------------------------------------------

* Redis.io support.
* Memcache single or multiple hosts and socket support.
* `APC support <http://pecl.php.net/package/APC>`_.
* Group caching cache by groups and flush by groups or individual keys.
* Disk cache support.
* Prepared statements ready, support dynamic building queries, auto detect types
* (i,d,s,b).
* Secure connections with SSL.
* `SQLite3 Encryption <http://sqlcipher.net>`_.
* Ability to use different cache types at the same time.
* Simple store of session on database (mysql/sqlite) or a cache like
* redis/memcache/apc.
* Easy to use/install/adapt.
* Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
* Support connections via unix_sockets.
* SQL queues.
* Export to CSV.
* Trace/measure everything enabling the debugger by just setting something like
* $db->debug(1).
* Works outof the box with Cloud databases Amazon RDS.
* Lazy database connection. Connect only when needed.


What & Why DALMP
---------------------

PHP and MySQL is one of the most popular combinations used in web applications,
sometimes this "combo" join forces with tools like: redis, memcache, APC, etc,
always trying to achieve the best possible performance.

Setting all this together becomes tricky, especially when your goals are "speed
& security" and you have a short deadline.

DALMP makes all this integration without hassle, offering several methods that
can help the developer to focus more in optimizing his 'SQL statements' rather
than worry about how to properly configure cache instances or about duplicating
current connections to the database.

One of the main goals of DALMP is to avoid complexity at all cost without
losing flexibility and performance. The main class uses the PHP `mysqli
extension <http://php.net/mysqli>`_, therefore there is not need to have the PDO extension (older version
of PHP didn't include PDO by default).

To take advantage of the cache class and methods it is suggested to install the
following extensions:

* redis: `http://github.com/nicolasff/phpredis <http://github.com/nicolasff/phpredis>`_
* memcache: `http://pecl.php.net/package/memcache <http://pecl.php.net/package/memcache>`_

On `FreeBSD <http://www.freebsd.org>`_ you can install DALMP from ports: /usr/ports/databases/dalmp
