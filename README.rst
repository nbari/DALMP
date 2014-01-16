DALMP
=====

**Database Abstraction Layer for MySQL using PHP**

0% fat and extremely easy to use. Only connect to database when needed.

Examples and documentation at: http://docs.dalmp.com


Clone the repository:

.. code-block:: sh

   $ git clone git://github.com/nbari/DALMP.git dalmp

.. seealso::

   `Install <http://docs.dalmp.com/en/latest/Install.html>`_


Details
.......

* `Dependecy Injector <http://docs.dalmp.com/en/latest/DI.html>`_ (DI) support, load once, trigger when required.
* `APC <http://docs.dalmp.com/en/latest/cache/APC.html>`_, `Disk <http://docs.dalmp.com/en/latest/cache/disk.html>`_, `Memcache <http://docs.dalmp.com/en/latest/cache/memcache.html>`_, `Redis.io <http://docs.dalmp.com/en/latest/cache/redis.html>`_ cache support.
* Group `caching cache <http://docs.dalmp.com/en/latest/cache.html>`_ by groups and flush by groups or individual keys.
* `Prepared statements <http://docs.dalmp.com/en/latest/prepared_statements.html>`_ ready, support dynamic building queries, auto detect types (i,d,s,b).
* Secure connections with `SSL <http://docs.dalmp.com/en/latest/Quickstart.html#ssl>`_.
* `SQLite3 Encryption <http://sqlcipher.net>`_.
* Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
* Easy to use/install/adapt.
* Nested `Transactions <http://docs.dalmp.com/en/latest/database/StartTrans.html>`_ (SAVEPOINT / ROLLBACK TO SAVEPOINT).
* Support connections via `unix_sockets <http://docs.dalmp.com/en/latest/Quickstart.html#example-using-a-socket>`_.
* SQL `queues <http://docs.dalmp.com/en/latest/queue.html>`_.
* Export to `CSV <http://docs.dalmp.com/en/latest/database/csv.html>`_.
* Trace/measure everything enabling the debugger by just setting something like `$db->debug(1) <http://docs.dalmp.com/en/latest/database/debug.html>`_.
* Works out of the box with Cloud databases like `Amazon RDS <http://aws.amazon.com/rds/>`_ or `Google cloud <https://developers.google.com/cloud-sql/>`_.
* Lazy database connection. Connect only when needed.
* `PSR-0 <http://www.php-fig.org/psr/psr-0/>`_ compliance.


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
