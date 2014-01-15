DALMP\\Sessions
===============

**DALMP** can store `PHP sessions <http://www.php.net/manual/en/features.sessions.php>`_` in a mysql/sqlite database or in a cache engine
like redis or memcache.

One of the advantage of storing the session on mysql or cache engine is the
ability to make your application more scalable, without hassle.

Besides the normal use of sessions, **DALMP** allows the creation of references
attached to a session, this means that in some cases you can take advantage of
the storage engine that keep the sessions for storing valued information.

For example, you can store in the reference, the current user id 'UID' and
configure your site to only accept users to loggin once avoiding with this
duplicate entries/access using the same user/password.

``DALMP\\Sessions`` implements the `SessionHandlerInterface class <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.

The current available backends are:

* Files
* Memcache
* MySQL
* Redis
* SQLite


.. toctree::
   :maxdepth: 2

   sessions/construct
   sessions/Files
   sessions/Memcache
   sessions/MySQL
   sessions/Redis
   sessions/SQLite
   sessions/regenerate_id
