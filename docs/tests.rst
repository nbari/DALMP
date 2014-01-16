Tests
=====

For testing **DALMP** load the `world.sql.gz <https://github.com/nbari/DALMP/blob/master/examples/world.sql.gz>`_ located at the examples dir:

.. code-block:: shell
   :linenos:

   gzcat examples/world.sql.gz | mysql -uroot dalmp


*You can try also with gzip, gunzip, zcat as alternative to gzcat*

That will load all the `world tables <http://dev.mysql.com/doc/index-other.html>`_ into the dalmp database and also create the
dalmp_sessions table.

For testing purposes the same DSN (same database) is used when testing sessions
and database, in practice you can have different DSN depending on your
requirements.

You can however configure your DNS::

    cp phpunit.xml.dist phpunit.xml

Edit the DSN section::

    ...
    <php>
        <var name="DSN" value="utf8://root@localhost:3306/dalmp" />
    </php>
    ...

Install `composer <http://getcomposer.org/>`_ and required packages::

    curl -sS https://getcomposer.org/installer | php -- --install-dir=bin

Install `phpunit <http://phpunit.de/>`_ via composer::

    ./bin/composer.phar install --dev

For example to test only the `Cache\\Memcache </en/latest/cache/memcache.html>`_::

    ./bin/phpunit --testsuite CacheMemcache --tap -c phpunit.xml

To run all the tests:

    ./bin/phpunit --tap -c phpunit.xml
