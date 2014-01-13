getAll / PgetAll
================

Executes the SQL and returns the all the rows as a 2-dimensional array. If an
error occurs, false is returned.

Parameters
..........

::

    getAll($sql)

:$sql: The MySQL query to perfom on the database

Prepared statements Parameters
..............................

::

    PgetAll($sql, $varN)

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

   /**
    * GetAll
    */
   rs = $db->FetchMode('ASSOC')->GetAll('SELECT name, continent FROM Country WHERE Region ="Caribbean"');

   /**
    * Prepared statements
    */
   $rs = $db->FetchMode('ASSOC')->PGetAll('SELECT name, continent FROM Country WHERE Region = ?', 'Caribbean');


   // output of print_r($rs)
   Array
   (
       [0] => Array
           (
               [name] => Aruba
               [continent] => North America
           )

       [1] => Array
           (
               [name] => Anguilla
               [continent] => North America
           )

       [2] => Array
           (
               [name] => Netherlands Antilles
               [continent] => North America
           )

       [3] => Array
           (
               [name] => Antigua and Barbuda
               [continent] => North America
           )

       [4] => Array
           (
               [name] => Bahamas
               [continent] => North America
           )

       [5] => Array
           (
               [name] => Barbados
               [continent] => North America
           )

       [6] => Array
           (
               [name] => Cuba
               [continent] => North America
           )

       [7] => Array
           (
               [name] => Cayman Islands
               [continent] => North America
           )

       [8] => Array
           (
               [name] => Dominica
               [continent] => North America
           )

       [9] => Array
           (
               [name] => Dominican Republic
               [continent] => North America
           )

       [10] => Array
           (
               [name] => Guadeloupe
               [continent] => North America
           )

       [11] => Array
           (
               [name] => Grenada
               [continent] => North America
           )

       [12] => Array
           (
               [name] => Haiti
               [continent] => North America
           )

       [13] => Array
           (
               [name] => Jamaica
               [continent] => North America
           )

       [14] => Array
           (
               [name] => Saint Kitts and Nevis
               [continent] => North America
           )

       [15] => Array
           (
               [name] => Saint Lucia
               [continent] => North America
           )

       [16] => Array
           (
               [name] => Montserrat
               [continent] => North America
           )

       [17] => Array
           (
               [name] => Martinique
               [continent] => North America
           )

       [18] => Array
           (
               [name] => Puerto Rico
               [continent] => North America
           )

       [19] => Array
           (
               [name] => Turks and Caicos Islands
               [continent] => North America
           )

       [20] => Array
           (
               [name] => Trinidad and Tobago
               [continent] => North America
           )

       [21] => Array
           (
               [name] => Saint Vincent and the Grenadines
               [continent] => North America
           )

       [22] => Array
           (
               [name] => Virgin Islands, British
               [continent] => North America
           )

       [23] => Array
           (
               [name] => Virgin Islands, U.S.
               [continent] => North America
           )
   )