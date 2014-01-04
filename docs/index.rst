DALMP
=====

**Database Abstraction Layer for MySQL using PHP**

0% fat and extremely easy to use. Only connect to database when needed.

Clone the repository::

    git clone git://github.com/nbari/DALMP.git dalmp


Details
.......

* Redis.io support.
* Memcache single or multiple hosts and socket support.
* `APC support <http://pecl.php.net/package/APC>`_.
* Group caching cache by groups and flush by groups or individual keys.
* Disk cache support.
* Prepared statements ready, support dynamic building queries, auto detect types (i,d,s,b).
* Secure connections with SSL.
* `SQLite3 Encryption <http://sqlcipher.net>`_.
* Ability to use different cache types at the same time.
* Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
* Easy to use/install/adapt.
* Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
* Support connections via unix_sockets.
* SQL queues.
* Export to CSV.
* Trace/measure everything enabling the debugger by just setting something like $db->debug(1).
* Works out of the box with Cloud databases like Amazon RDS.
* Lazy database connection. Connect only when needed.


Requirements
............

`PHP <http://www.php.net>`_ >= 5.4

To use the cache features you need either the redis, memcache or APC extensions
compiled, otherwise disk cache will be used.

* Redis extension - http://github.com/nicolasff/phpredis
* Memcache PECL extencsion - http://pecl.php.net/package/memcache
* APC PECL extension - http://pecl.php.net/package/APC

If you want to store session encrypted then you need SQLite3 Encryption
(http://sqlcipher.net).

**DALMP** does not use `PDO <http://www.php.net/pdo>`_, so do not worry if your PHP does not have the pdo
extension.

A MySQL server to connect via host/socket

On `FreeBSD <http://www.freebsd.org>`_ you can install **DALMP** from ports: /usr/ports/databases/dalmp



Table of Contents
=================

.. toctree::
   :maxdepth: 2

   about
   Download
   Install
   Quickstart
   dalmp
   cache
   queue
   sessions
   tests
