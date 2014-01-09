DALMP
=====

**Database Abstraction Layer for MySQL using PHP**

0% fat and extremely easy to use. Only connect to database when needed.

Clone the repository:

.. code-block:: sh

   $ git clone git://github.com/nbari/DALMP.git dalmp


Details
.......

* `Redis.io </en/latest/cache/redis.html>`_ support.
* `Memcache </en/latest/cache/memcache.html>`_ support.
* `APC </en/latest/cache/APC.html>`_ support.
* `Disk </en/latest/cache/disk.html>`_ cache support.
* Group `caching cache </en/latest/cache.html>`_ by groups and flush by groups or individual keys.
* `Prepared statements </en/latest/prepared_statements.html>`_ ready, support dynamic building queries, auto detect types (i,d,s,b).
* Secure connections with `SSL </en/latest/Quickstart.html#ssl>`_.
* `SQLite3 Encryption <http://sqlcipher.net>`_.
* Ability to use different cache types at the same time.
* Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
* Easy to use/install/adapt.
* Nested `Transactions </en/latest/database/StartTrans.html>`_ (SAVEPOINT / ROLLBACK TO SAVEPOINT).
* Support connections via `unix_sockets </en/latest/Quickstart.html#example-using-a-socket>`_
* SQL `queues </en/latest/queue.html>`_.
* Export to `CSV </en/latest/database/csv.html>`_.
* Trace/measure everything enabling the debugger by just setting something like `$db->debug(1) </en/latest/database/debug.html>`_.
* Works out of the box with Cloud databases like `Amazon RDS <http://aws.amazon.com/rds/>`_ or `Google cloud <https://developers.google.com/cloud-sql/>`_.
* Lazy database connection. Connect only when needed.


Requirements
............

* `PHP <http://www.php.net>`_ >= 5.4

* A `MySQL <http://www.mysql.org>`_ server to connect via host or `unix sockets. <http://en.wikipedia.org/wiki/Unix_domain_socket>`_

To use the cache features you need either the redis, memcache or APC extensions
compiled, otherwise disk cache will be used.

* Redis extension - http://github.com/nicolasff/phpredis
* Memcache PECL extencsion - http://pecl.php.net/package/memcache
* APC PECL extension - http://pecl.php.net/package/APC

If you want to store session encrypted then you need SQLite3 Encryption
(http://sqlcipher.net).

**DALMP** does not use `PDO <http://www.php.net/pdo>`_, so do not worry if your PHP does not have the pdo
extension.

On `FreeBSD <http://www.freebsd.org>`_ you can install **DALMP** from ports: /usr/ports/databases/dalmp


Table of Contents
=================

.. toctree::
   :maxdepth: 2

   about
   Download
   Install
   Quickstart
   database
   cache
   queue
   sessions
   prepared_statements
   DI
   tests
   examples
