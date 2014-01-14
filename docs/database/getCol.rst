getCol / PgetCol
================

Executes the SQL and returns all elements of the first column as a
1-dimensional array.

If an error occurs, false is returned.

Parameters
..........

::

    getCol($sql)

:$sql: The MySQL query to perfom on the database

Prepared statements Parameters
..............................

::

    PgetCol($sql, $varN)

:$sql: The MySQL query to perfom on the database
:$varN: The variable(s) that will be placed instead of the **?** placeholder separated by a ',' or it can be the method `Prepare </en/latest/database/Prepare.html>`_.

Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 15, 20

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $rs = $db->PGetCol('SELECT name FROM Country WHERE Region = ?', 'Caribbean');

Output of ``print_r($rs)``:

.. code-block:: rest
   :linenos:

   Array
   (
       [0] => Aruba
       [1] => Anguilla
       [2] => Netherlands Antilles
       [3] => Antigua and Barbuda
       [4] => Bahamas
       [5] => Barbados
       [6] => Cuba
       [7] => Cayman Islands
       [8] => Dominica
       [9] => Dominican Republic
       [10] => Guadeloupe
       [11] => Grenada
       [12] => Haiti
       [13] => Jamaica
       [14] => Saint Kitts and Nevis
       [15] => Saint Lucia
       [16] => Montserrat
       [17] => Martinique
       [18] => Puerto Rico
       [19] => Turks and Caicos Islands
       [20] => Trinidad and Tobago
       [21] => Saint Vincent and the Grenadines
       [22] => Virgin Islands, British
       [23] => Virgin Islands, U.S.
   )