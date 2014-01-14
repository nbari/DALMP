map
===

Maps the result to an object.

Parameters
..........

::

   map($sql, $class_name=null, $params=array())


:$sql: The MySQL query to perfom on the database.
:$class_name: The name of the class to instantiate, set the properties of and return. If not specified, a stdClass object is returned.
:$params: An optional array of parameters to pass to the constructor for **$class_name** objects.


Example
.......



.. seealso::

   `mysqli_fetch_object <http://www.php.net/manual/en/mysqli-result.fetch-object.php>`_.