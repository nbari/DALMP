getNumOfFields
==============

Returns the number of columns for the most recent query.


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

   $rs = $db->FetchMode('ASSOC')->PGetRow('SELECT * FROM Country WHERE Region = ? LIMIT 1', 'Caribbean');

   // output of print_r($rs);
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

   echo $db->getNumOfFields(); // return 15