Prepared Statements
===================

`Prepared Statements <http://en.wikipedia.org/wiki/Prepared_statement>`_ help
you in many cases to avoid avoid mysql injections and helps increasing security
of your queries by separating the SQL logic from the data being supplied.

`DALMP\\Database </en/latest/database.html>`_ by default tries to determine the type of the data supplied, so you can
just focus on your query without needing to specify the type of data, If you
preffer you can manually specify the type of the data.

The following table, show the characters which specify the types for the corresponding bind
variables:


+-----------+--------------------------------------------------------------+
| Character | Description                                                  |
+===========+==============================================================+
| i         | corresponding variable has type integer                      |
+-----------+--------------------------------------------------------------+
| d         | corresponding variable has type double                       |
+-----------+--------------------------------------------------------------+
| s         | corresponding variable has type string                       |
+-----------+--------------------------------------------------------------+
| b         | corresponding variable is a blob and will be sent in packets |
+-----------+--------------------------------------------------------------+

In most cases you probably only use this method when Inserting or Updating
data, for retrieving data you can use the same methods used for the Cache method
which are: