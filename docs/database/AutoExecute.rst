AutoExecute
===========

Automatically prepares and runs an INSERT or UPDATE query based on variables
you supply.

This is very usefull when you simple want to save post data from a huge web
form, AutoExecute will genereate a mysql prepared statement from the array used
and INSERT or UPDATE

Parameters
..........

::

    AutoExecute($table, $fields, $mode = 'INSERT', $where = null)

:$table: The name of the table you want to INSERT or UPDATE

:$fields: An assoc array (key => value), keys are fields names, values are values of these fields.

:$mode: INSERT or UPDATE

:$where: A string to be used in the WHERE clause. This is only used when $mode = UPDATE.