DALMP
=====

**Database Abstraction Layer for MySQL using PHP**

0% fat and extremely easy to use. Only connect to database when needed.

Examples and documentation at: http://docs.dalmp.com


Clone the repository:


    $ git clone https://github.com/nbari/DALMP.git dalmp


To Install visit  http://docs.dalmp.com/en/latest/Install.html


Details
-------

* [Dependecy Injector](http://docs.dalmp.com/en/latest/DI.html) (DI) support, load once, trigger when required.
* [APC](http://docs.dalmp.com/en/latest/cache/APC.html), [Disk](http://docs.dalmp.com/en/latest/cache/disk.html), [Memcache](http://docs.dalmp.com/en/latest/cache/memcache.html), [Redis.io](http://docs.dalmp.com/en/latest/cache/redis.html>) cache support.
* Group [caching cache](http://docs.dalmp.com/en/latest/cache.html) by groups and flush by groups or individual keys.
* [Prepared statements](http://docs.dalmp.com/en/latest/prepared_statements.html) ready, support dynamic building queries, auto detect types (i,d,s,b).
* Secure connections with [SSL](http://docs.dalmp.com/en/latest/Quickstart.html#ssl).
* [SQLite3 Encryption](http://docs.dalmp.com/en/latest/queue/SQLite.html).
* Save [sessions in database](http://docs.dalmp.com/en/latest/sessions.html) (mysql/sqlite) or a cache like redis/memcache/apc.
* Easy to use/install/adapt.
* Nested [Transactions](http://docs.dalmp.com/en/latest/database/StartTrans.html) (SAVEPOINT / ROLLBACK TO SAVEPOINT).
* Support connections via [unix_sockets](http://docs.dalmp.com/en/latest/Quickstart.html#example-using-a-socket).
* SQL [queues](http://docs.dalmp.com/en/latest/queue.html).
* Export to [CSV](http://docs.dalmp.com/en/latest/database/csv.html).
* Trace/measure everything enabling the [debugger](http://docs.dalmp.com/en/latest/database/debug.htm).
* Works out of the box with Cloud databases like [Amazon RDS](http://aws.amazon.com/rds/) or [Google cloud](https://developers.google.com/cloud-sql/).
* Lazy database connection. Connect only when needed.
* [PSR-0](http://www.php-fig.org/psr/psr-0/) compliance.


Requirements
------------

* [PHP](http://www.php.net>) >= 5.4
* A [MySQL](http://www.mysql.org) server to connect via host or [unix sockets](http://en.wikipedia.org/wiki/Unix_domain_socket).

To use the cache features you need either the redis, memcache or APC extensions
compiled, otherwise disk cache will be used.

* Redis extension - http://github.com/nicolasff/phpredis
* Memcache PECL extencsion - http://pecl.php.net/package/memcache
* APC PECL extension - http://pecl.php.net/package/APC

If you want to store session encrypted then you need SQLite3 Encryption http://sqlcipher.net.

**DALMP** does not use [PDO](http://www.php.net/pdo), so do not worry if your PHP does not have the pdo
extension.

On [FreeBSD](http://www.freebsd.org) you can install **DALMP** from ports: /usr/ports/databases/dalmp

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/nbari/dalmp/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
