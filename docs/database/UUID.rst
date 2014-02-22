UUID
====

Generates an Universally Unique Identifier (`UUID <http://en.wikipedia.org/wiki/Universally_unique_identifier>`_) v4.

Parameters
..........

::

    UUID($b=null)

:$b: If true will return the UUID in binary, removing the dashes so that you can store it on a DB using column data type binary(16).

Examples
........

.. code-block:: php
   :linenos:

   <?php
   ...

   echo $db->UUID();

   echo $db->UUID(true);


Example storing UUID as binary(16):

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $uuid = $db->UUID();

   $db->PExecute("INSERT INTO table (post, uuid) VALUES(?, UNHEX(REPLACE(?, '-', '')))", json_encode($_POST), $uuid);


Example converting from binary(16) to original UUID format chat(36):

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $sql = "SELECT LOWER(CONCAT_WS('-',LEFT(HEX(uuid),8),SUBSTR(HEX(uuid),9,4),SUBSTR(HEX(uuid),13,4),SUBSTR(HEX(uuid),17,4),RIGHT(HEX(uuid),12))) FROM table";

   $uuids = $db->FetchMode('ASSOC')->getCol($sql);


.. seealso::

   `MySQL string functions <http://dev.mysql.com/doc/refman/5.0/en/string-functions.html#function_unhex>`_.