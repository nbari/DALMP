DALMP - Database Abstraction Layer for MySQL using PHP 
======================================================

%0 fat and extremely easy to use, just one file, define some constants and you are ready to go.

Details
=======

	*	*redis* support (http://code.google.com/p/redis/)
	* *memcache* single or multiple hosts and socket support (http://code.google.com/p/memcached/)
	* *apc* support (http://pecl.php.net/package/APC)
	* *Group caching* cache by groups and flush by groups or individual keys
	*	Disk cache support.
	* Prepared statements ready, support dynamic building queries.
	* Secure connections with SSL.
	*	Ability to use different cache types at the same time.
	* Simple store of session on database (mysql/sqlite) or a cache like redis/memcache/apc.
	*	Easy to use/install/adapt DALMP is just a single file.
	* Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
	* support connections via unix_sockets 
	* SQL queues.
	* helpful methods, renumber('table') or renumber('table','uid') - renumbers a table, UUID - create an 'universally unique identifiers'
	* http client + queue (for sending data via http to another server expecting an answer, if expected was ok then proceed other wise queue the http request).
	* trace/measure everything enabling the debugger by just setting something like $db->debug(1).
	* works with Cloud databases like Xeround & Amazon RDS out of the box

_share knowledge:_ irc.freenode.net #dalmp

*FreeBSD*
Install from ports: /usr/ports/databases/dalmp