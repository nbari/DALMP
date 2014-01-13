getColumnNames
==============

Return the name of the tables.


Parameters
..........

::

    getColumnNames($tablename)

:$tablename: Name of the table to get the column names.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 15,

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   // output of print_r($rs);
   Array
   (
       [0] => Code
       [1] => Name
       [2] => Continent
       [3] => Region
       [4] => SurfaceArea
       [5] => IndepYear
       [6] => Population
       [7] => LifeExpectancy
       [8] => GNP
       [9] => GNPOld
       [10] => LocalName
       [11] => GovernmentForm
       [12] => HeadOfState
       [13] => Capital
       [14] => Code2
   )