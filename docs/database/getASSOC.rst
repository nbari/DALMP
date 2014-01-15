getASSOC / PgetASSOC
====================

Executes the SQL and returns an associative array for the given query.

If the number of columns returned is greater to two, a 2-dimensional array is
returned, with the first column of the recordset becomes the keys to the rest
of the rows. If the columns is equal to two, a 1-dimensional array is created,
where the the keys directly map to the values.

If an error occurs, false is returned.


Parameters
..........

::

    getASSOC($sql)

:$sql: The MySQL query to perfom on the database.

Prepared statements Parameters
..............................

::

    PgetASSOC($sql, $varN)

:$sql: The MySQL query to perfom on the database
:$varN: The variable(s) that will be placed instead of the **?** placeholder separated by a ',' or it can be the method `Prepare </en/latest/database/Prepare.html>`_.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->PGetASSOC('SELECT name, continent FROM Country WHERE Region = ?', 'Caribbean');

Output of ``print_r($rs)``:

.. code-block:: rest
   :linenos:

   Array
   (
       [Aruba] => North America
       [Anguilla] => North America
       [Netherlands Antilles] => North America
       [Antigua and Barbuda] => North America
       [Bahamas] => North America
       [Barbados] => North America
       [Cuba] => North America
       [Cayman Islands] => North America
       [Dominica] => North America
       [Dominican Republic] => North America
       [Guadeloupe] => North America
       [Grenada] => North America
       [Haiti] => North America
       [Jamaica] => North America
       [Saint Kitts and Nevis] => North America
       [Saint Lucia] => North America
       [Montserrat] => North America
       [Martinique] => North America
       [Puerto Rico] => North America
       [Turks and Caicos Islands] => North America
       [Trinidad and Tobago] => North America
       [Saint Vincent and the Grenadines] => North America
       [Virgin Islands, British] => North America
       [Virgin Islands, U.S.] => North America
   )