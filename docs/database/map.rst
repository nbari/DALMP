map
===

Maps the result to an object.

Parameters
..........

::

   map($sql, $class_name=null, $params=array())


:$sql: The MySQL query to perfom on the database.
:$class_name: The name of the class to instantiate, set the properties of and return. If not specified, a `stdClass <http://www.php.net/manual/en/reserved.classes.php>`_ object is returned.
:$params: An optional array of parameters to pass to the constructor for **$class_name** objects.


Example
.......

.. code-block:: php
   :linenos:
   :emphasize-lines: 11

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';
   $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
   $port = getenv('MYSQL_PORT') ?: '3306';

   $db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

   $db->FetchMode('ASSOC');
   $ors = $db->map('SELECT * FROM City WHERE Name="Toluca"');

   echo sprintf('ID: %d CountryCode: %s', $ors->ID, $ors->CountryCode);

   print_r($ors);

   // output
   ID: 2534 CountryCode: MEX

   stdClass Object
   (
       [ID] => 2534
       [Name] => Toluca
       [CountryCode] => MEX
       [District] => México
       [Population] => 665617
   )

.. seealso::

   `mysqli_fetch_object <http://www.php.net/manual/en/mysqli-result.fetch-object.php>`_.