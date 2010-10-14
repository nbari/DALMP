DALMP - Database Abstraction Layer for MySQL using PHP 
======================================================

After many years of web developing with PHP, 99% of my sites are using php and mysql so that is why I decided to use create DALMP, a simple database abstraction layer that extends php mysqli extension.

I have been using ADOdb, pear DB, Zend DB,  etc. they are excellent applications, but many times they are to much for what I need, so that is why I started creating DALMP, an data abstraction layer that just feet my needs , taking the best practice code out there and put it on a single simple file.

Details
=======

  * *memcache* support (http://code.google.com/p/memcached/)
  * *apc* support (http://pecl.php.net/package/APC)
  * *redis* support (http://github.com/owlient/phpredis)
  * disk cache support 
  * ability to use different cache types at the same time 
  * simple store of session on database and memcache
  * common methods are named exactly like ADOdb in case you want to try DALMP with an existing code that uses ADOdb
  * sql queue
  * http client + queue (for sending data via http to another server expecting an answer, if expected was ok then proceed other wise queue the http request)
