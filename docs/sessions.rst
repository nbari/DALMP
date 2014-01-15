DALMP\\Sessions
===============

**DALMP** can store `PHP sessions <http://www.php.net/manual/en/features.sessions.php>`_ in a mysql/sqlite database or in a cache engine
like redis or memcache.

One of the advantage of storing the session on mysql or cache engine is the
ability to make your application more scalable, without hassle.

Besides the normal use of sessions, **DALMP** allows the creation of references
attached to a session, this means that in some cases you can take advantage of
the storage engine that keep the sessions for storing valued information.

The methods you can use to handle references stored on the sessions are:

=============== ==========================================================
Method          Description
=============== ==========================================================
getSessionsRefs return array of sessions containing any reference.
getSessionRef   return array of sessions containing a specific reference.
delSessionRef   delete sessions containing a specific reference.
=============== ==========================================================

For example, you can store in the reference, the current user id 'UID' and
configure your site to only accept users to loggin once avoiding with this
duplicate entries/access using the same user/password.

``DALMP\Sessions`` implements the `SessionHandlerInterface class <http://www.php.net/manual/en/class.sessionhandlerinterface.php>`_.

The current available backends are:

======== =================================================================
Backend  Description
======== =================================================================
Files    Use file system to store the sessions.
Memcache Use memcache `DALMP\\Cache </en/latest/cache/memcache.html>`_
MySQL    Use MySQL database `DALMP\\Database </en/latest/database.html>`_.
Redis    Use redis `DALMP\\Cache </en/latest/cache/redis.html>`_.
SQLite   Use SQLite.
======== =================================================================


**See Also:**

.. toctree::
   :maxdepth: 2

   sessions/construct
   sessions/Files
   sessions/Memcache
   sessions/MySQL
   sessions/Redis
   sessions/SQLite
   sessions/regenerate_id
   sessions/getSessionsRefs
   sessions/getSessionRef
   sessions/delSessionRef

.. warning::

   In order to properly use ``DALMP\Sessions`` you need (PHP 5 >= 5.4.0).
