DALMP - Database Abstraction Layer for MySQL using PHP 
======================================================

%0 fat and extremely easy to use, only connect to database when needed.

Details
=======

  * *redis* support (http://code.google.com/p/redis/)
  * *memcache*  single or multiple hosts and socket support (http://code.google.com/p/memcached/)
  * *apc* support (http://pecl.php.net/package/APC)
  * *Group caching*  cache by groups and flush by groups or individual keys
  * Disk cache support.
  * Prepared statements ready, support dynamic building queries, auto detect types (i,d,s,b).
  * Secure connections with SSL.
  * SQLite3 Encryption (http://sqlcipher.net)
  * Ability to use different cache types at the same time.
  * Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
  * Easy to use/install/adapt. 
  * Nested Transactions (SAVEPOINT  / ROLLBACK TO SAVEPOINT).
  * support connections via unix_sockets.
  * SQL queues.
  * trace/measure everything enabling the debugger by just setting something like $db->debug(1).
  * works with Cloud databases like  Xeround & Amazon RDS out of the box.
  * lazy database connection.

*FreeBSD*
Install from ports: /usr/ports/databases/dalmp