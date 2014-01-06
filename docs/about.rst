DALMP
=====

**Database Abstraction Layer for MySQL using PHP**


What & Why DALMP
................

`PHP <http://www.php.net>`_  and `MySQL <http://www.mysql.org>`_ is one of the most popular combinations used in web applications,
sometimes this "combo" join forces with tools like: redis, memcache, APC, etc,
always trying to achieve the best possible performance.

Setting all this together becomes tricky, especially when your goals are "speed
& security" and you have a short deadline.

DALMP makes all this integration without hassle, offering several methods that
can help the developer to focus more in optimizing his 'SQL statements' rather
than worry about how to properly configure cache instances or about duplicating
current connections to the database.

One of the main goals of **DALMP** is to avoid complexity at all cost without
losing flexibility and performance. The main class uses the PHP
`mysqli extension <http://php.net/mysqli>`_, therefore there is not need
to have the `PDO <http://www.php.net/pdo>`_ extension (older version of PHP didn't include PDO by default).

To take advantage of the cache class and methods it is suggested to install the
following extensions:

* redis: `http://github.com/nicolasff/phpredis <http://github.com/nicolasff/phpredis>`_
* memcache: `http://pecl.php.net/package/memcache <http://pecl.php.net/package/memcache>`_

If you have a site on the cloud or in a load balanced enviroment, you could
take advantege of how DALMP handle sessions by storing them in a database or in
a cache engine.


.. seemore::

   `Dalmp\\Sessions </en/latest/sessions.html>`_



On `FreeBSD <http://www.freebsd.org>`_ you can install **DALMP** from ports: /usr/ports/databases/dalmp
