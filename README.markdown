DALMP
=====

Database Abstraction Layer for MySQL using PHP

0% fat and extremely easy to use, only connect to database when needed.

handbook: www.dalmp.com

Details
-------

  * Redis.io support.
  * Memcache single or multiple hosts and socket support.
  * APC support (http://pecl.php.net/package/APC).
  * Group caching cache by groups and flush by groups or individual keys.
  * Disk cache support.
  * Prepared statements ready, support dynamic building queries, auto detect types (i,d,s,b).
  * Secure connections with SSL.
  * SQLite3 Encryption (http://sqlcipher.net).
  * Simple store of session on database (mysql/sqlite) or a cache like redis/memcache.
  * Easy to use/install/adapt.
  * Nested Transactions (SAVEPOINT / ROLLBACK TO SAVEPOINT).
  * Support connections via unix_sockets.
  * SQL queues.
  * Export to CSV.
  * Trace/measure everything enabling the debugger by just setting something like $db->debug(1).
  * Works outof the box with Cloud databases like Amazon RDS.
  * Lazy database connection. Connect only when needed.
  * PSR-0 compliance.

*FreeBSD*
Install from ports: /usr/ports/databases/dalmp

Sessions
--------

For storing PHP sessions on mysql you need to create a table with the following schema

    CREATE TABLE IF NOT EXISTS `dalmp_sessions` (
    `sid` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `expiry` int(11) unsigned NOT NULL DEFAULT '0',
    `data` longtext CHARACTER SET utf8 COLLATE utf8_bin,
    `ref` varchar(255) DEFAULT NULL,
    `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sid`),
    KEY `index` (`ref`,`sid`,`expiry`)
    ) DEFAULT CHARSET=utf8;

Tests
-----

Install composer and required packages:

    curl -sS https://getcomposer.org/installer | php -- --install-dir=bin

Install phpunit via composer:

    ./bin/composer.phar install --dev

For example to test only the Cache\Memcache:

    ./bin/phpunit --testsuite CacheMemcache --tap -c phpunit.xml

To run all the tests:

    ./bin/phpunit --tap -c phpunit.xml

For testing the session_handler that uses mysql you need to edit phpunit.xml
file and enter your database DSN:

    DSN format: charset://username:password@host:port/database
    $db = new DALMP\Database('utf8://user:password@host:3306/your_database');

    ...
    <php>
      <var name="DSN" value="utf8://root@localhost:3306/dalmp" />
    </php>
    ...

For testing the database load the world.sql.gz located at the examples dir:

    gzcat examples/world.sql.gz | mysql -uroot dalmp

That will load all the world tables into the dalmp database.

For testing purposes the same DSN (same database) is used when testing sessions
and database, in practice you can have different DSN depending on your
requirements.


Bugs / suggestions / comments
-----------------------------

If you found a bug or have any other inquiries please use the the DALMP group at: https://groups.google.com/group/dalmp
