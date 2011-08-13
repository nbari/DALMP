DALMP - Database Abstraction Layer for MySQL using PHP 
======================================================

After many years of web developing with PHP, 99% of my sites are using MySQL so that is why I decided to create DALMP.

I have been using ADOdb, pear DB, Zend DB,  etc. they are excellent applications, but many times they are too much for what I need, so that is why I started creating DALMP, a data abstraction layer that just feet my needs , taking the best practice code out there and put it on a single simple file.

%0 fat and extremely easy to use, just one file, define some constants and you are ready to go.

Details
=======

  * *redis* support (http://code.google.com/p/redis/)
  * *memcache*  single or multiple hosts and socket support (http://code.google.com/p/memcached/)
  * *apc* support (http://pecl.php.net/package/APC)
  * *Group caching* cache by groups and flush by groups or individual keys 
  * Disk cache support.
  * Prepared statements ready.
  * Secure connections with SSL.
  * Ability to use different cache types at the same time.
  * Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
  * Easy to use/install/adapt DALMP is just a single file. 
  * Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
  * Common methods are named exactly like ADOdb in case you want to try DALMP with an existing code that uses ADOdb.
  * sql queue.
  * helpful methods, renumber('table') or renumber('table','uid') - renumbers a table, UUID - create an 'universally unique identifiers'. 
  * http client + queue (for sending data via http to another server expecting an answer, if expected was ok then proceed other wise queue the http request).
  * trace everything enabling the debugger by just setting something like $db->debug(1).

_share knowledge:_ irc.freenode.net #dalmp
