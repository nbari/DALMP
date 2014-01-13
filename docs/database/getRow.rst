getRow / PgetRow
================

Executes the SQL and returns the first field of the first row. If an error
occurs, false is returned.

Parameters
..........

::

    getRow($sql)


:$sql: The MySQL query to perfom on the database

Prepared statements Parameters
..............................

::

    PgetRow($sql, $varN)

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

   $rs = $db->PGetRow('SELECT * FROM Country WHERE Region = ? LIMIT 1', 'Caribbean');

   // output of print_r($rs)
   Array
   (
       [0] => ABW
       [Code] => ABW
       [1] => Aruba
       [Name] => Aruba
       [2] => North America
       [Continent] => North America
       [3] => Caribbean
       [Region] => Caribbean
       [4] => 193
       [SurfaceArea] => 193
       [5] =>
       [IndepYear] =>
       [6] => 103000
       [Population] => 103000
       [7] => 78.400001525879
       [LifeExpectancy] => 78.400001525879
       [8] => 828
       [GNP] => 828
       [9] => 793
       [GNPOld] => 793
       [10] => Aruba
       [LocalName] => Aruba
       [11] => Nonmetropolitan Territory of The Netherlands
       [GovernmentForm] => Nonmetropolitan Territory of The Netherlands
       [12] => Beatrix
       [HeadOfState] => Beatrix
       [13] => 129
       [Capital] => 129
       [14] => AW
       [Code2] => AW
   )

Same query but using FetchMode('ASSOC')

.. code-block:: php
   :linenos:
   :emphasize-lines: 3

   <?php
   ...
   $rs = $db->FetchMode('ASSOC')->PGetRow('SELECT * FROM Country WHERE Region = ? LIMIT 1', 'Caribbean');

   // output of print_r($rs)
   Array
   (
       [Code] => ABW
       [Name] => Aruba
       [Continent] => North America
       [Region] => Caribbean
       [SurfaceArea] => 193
       [IndepYear] =>
       [Population] => 103000
       [LifeExpectancy] => 78.400001525879
       [GNP] => 828
       [GNPOld] => 793
       [LocalName] => Aruba
       [GovernmentForm] => Nonmetropolitan Territory of The Netherlands
       [HeadOfState] => Beatrix
       [Capital] => 129
       [Code2] => AW
   )