FetchMode
=========

This 'chainable' method indicates what type of array should be returned.


Parameters
..........

::

   FetchMode($mode = null)

:$mode: can be NUM (MYSQLI_NUM), ASSOC (MYSQLI_ASSOC) or if not set, it will use both (MYSQLI_BOTH).

If two or more columns of the result have the same field names, the last column
will take precedence. To access the other column(s) of the same name, you e
need to access the result with numeric indices by using **FetchMode('NUM')** or add
alias names.

Examples
........

.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->FetchMode('NUM');

   $rs = $db->PGetAll('SELECT * FROM Country WHERE Region = ?', 'Caribbean');


Chainable example
..................


.. code-block:: php
   :linenos:
   :emphasize-lines: 12

   <?php

   require_once 'dalmp.php';

   $user = getenv('MYSQL_USER') ?: 'root';
   $password = getenv('MYSQL_PASS') ?: '';

   $DSN = "utf8://$user:$password".'@127.0.0.1/test';

   $db = new DALMP\Database($DSN);

   $db->FetchMode('NUM')->PGetAll('SELECT * FROM Country WHERE Region = ?', 'Caribbean');

When using NUM the keys of the result array are numeric. Example of the output:

.. code-block:: rest
   :linenos:

   // output of print($rs);
   Array
   (
       [0] => Array
           (
               [0] => ABW
               [1] => Aruba
               [2] => North America
               [3] => Caribbean
               [4] => 193
               [5] =>
               [6] => 103000
               [7] => 78.400001525879
               [8] => 828
               [9] => 793
               [10] => Aruba
               [11] => Nonmetropolitan Territory of The Netherlands
               [12] => Beatrix
               [13] => 129
               [14] => AW
           )

       [1] => Array
           (
               [0] => AIA
               [1] => Anguilla
               [2] => North America
               [3] => Caribbean
               [4] => 96
               [5] =>
               [6] => 8000
               [7] => 76.099998474121
               [8] => 63.200000762939
               [9] =>
               [10] => Anguilla
               [11] => Dependent Territory of the UK
               [12] => Elisabeth II
               [13] => 62
               [14] => AI
           )
       ...


ASSOC mode, example
...................

.. code-block:: php
   :linenos:

   <?php
   ...
   $rs = $db->FetchMode('ASSOC')->PGetAll('SELECT * FROM Country WHERE Region = ?', 'Caribbean');

The output would be something like:

.. code-block:: rest
   :linenos:

   // output of print($rs);
   Array
   (
       [0] => Array
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

       [1] => Array
           (
               [Code] => AIA
               [Name] => Anguilla
               [Continent] => North America
               [Region] => Caribbean
               [SurfaceArea] => 96
               [IndepYear] =>
               [Population] => 8000
               [LifeExpectancy] => 76.099998474121
               [GNP] => 63.200000762939
               [GNPOld] =>
               [LocalName] => Anguilla
               [GovernmentForm] => Dependent Territory of the UK
               [HeadOfState] => Elisabeth II
               [Capital] => 62
               [Code2] => AI
           )
   ...

No mode
.......

When No mode is defined, the default is to use 'both' (MYSQLI_BOTH). example:

.. code-block:: php
   :linenos:

   <?php
   ...
   $rs = $db->PGetAll('SELECT * FROM Country WHERE Region = ?', 'Caribbean');

In this case the output is like:

.. code-block:: rest
   :linenos:

   // output of print($rs);
   Array
   (
       [0] => Array
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

       [1] => Array
           (
               [0] => AIA
               [Code] => AIA
               [1] => Anguilla
               [Name] => Anguilla
               [2] => North America
               [Continent] => North America
               [3] => Caribbean
               [Region] => Caribbean
               [4] => 96
               [SurfaceArea] => 96
               [5] =>
               [IndepYear] =>
               [6] => 8000
               [Population] => 8000
               [7] => 76.099998474121
               [LifeExpectancy] => 76.099998474121
               [8] => 63.200000762939
               [GNP] => 63.200000762939
               [9] =>
               [GNPOld] =>
               [10] => Anguilla
               [LocalName] => Anguilla
               [11] => Dependent Territory of the UK
               [GovernmentForm] => Dependent Territory of the UK
               [12] => Elisabeth II
               [HeadOfState] => Elisabeth II
               [13] => 62
               [Capital] => 62
               [14] => AI
               [Code2] => AI
           )
   ...


.. seealso::

  PHP `mysqli_fetch_array <http://www.php.net/mysqli_fetch_array>`_ &
  `mysqli_stmt_fetch <http://www.php.net/manual/en/mysqli-stmt.fetch.php>`_