DALMP\\Database
==============

The ``DALMP\Database`` class contains a set of `methods <https://github.com/nbari/DALMP/blob/master/src/DALMP/Database.php>`_ that allow to query a
database in a more easy and secure way.

The next table contains, the 5 methods, that return the same result, but behave
different, depending on the query or for particular cases, the developer should
chose the proper one.

+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| Name | Normal                                          | Prepared Statements | Cache Normal  | Cache Prepared Statements |
+======+=================================================+=====================+===============+===========================+
| all  | `GetAll </en/latest/database/getAll.html>`_     | PGetAll             | CacheGetAll   | CachePGetAll              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| assoc| `GetAssoc </en/latest/database/getASSOC.html>`_ | PGetAssoc           | CacheGetAssoc | CachePGetAssoc            |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| col  | `GetCol </en/latest/database/getCol.html>`_     | PGetCol             | CacheGetCol   | CachePGetCol              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| one  | `GetOne </en/latest/database/getOne.html>`_     | PGetOne             | CacheGetOne   | CachePGetOne              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+
| row  | `GetRow </en/latest/database/getRow.html>`_     | PGetRow             | CacheGetRow   | CachePGetRow              |
+------+-------------------------------------------------+---------------------+---------------+---------------------------+



.. toctree::
   :maxdepth: 2

   database/construct
   database/AutoExecute
   database/Cache
   database/CacheFlush
   database/CompleteTrans
   database/ErrorMsg
   database/ErrorNum
   database/Execute
   database/FetchMode
   database/Insert_Id
   database/PClose
   database/PExecute
   database/PQuery
   database/Prepare
   database/RollBackTrans
   database/StartTrans
   database/UUID
   database/closeConnection
   database/connect
   database/csv
   database/debug
   database/forceTruncate
   database/getAll
   database/getASSOC
   database/getCol
   database/getOne
   database/getRow
   database/getColumnNames
   database/getClientVersion
   database/getNumOfFields
   database/getNumOfRows
   database/getNumOfRowsAffected
   database/getServerVersion
   database/isConnected
   database/map
   database/multipleInsert
   database/qstr
   database/query
   database/renumber
   database/useCache
   database/X
