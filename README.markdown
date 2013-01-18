DALMP - Database Abstraction Layer for MySQL using PHP
======================================================

0% fat and extremely easy to use, only connect to database when needed.

manual: www.dalmp.com

Details
=======

  * Redis.io support.
  * Memcache single or multiple hosts and socket support.
  * APC support (http://pecl.php.net/package/APC).
  * Group caching cache by groups and flush by groups or individual keys.
  * Disk cache support.
  * Prepared statements ready, support dynamic building queries, auto detect types (i,d,s,b).
  * Secure connections with SSL.
  * SQLite3 Encryption (http://sqlcipher.net).
  * Ability to use different cache types at the same time.
  * Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
  * Easy to use/install/adapt.
  * Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
  * Support connections via unix_sockets.
  * SQL queues.
  * Export to CSV.
  * Trace/measure everything enabling the debugger by just setting something like $db->debug(1).
  * Works outof the box with Cloud databases like Xeround & Amazon RDS.
  * Lazy database connection. Connect only when needed.

*FreeBSD*
Install from ports: /usr/ports/databases/dalmp