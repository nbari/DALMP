DALMP\\Database
==============

The ``DALMP\Database`` class contains a set of methods that allow to query a
database in a more easy and secure way.

The next table contains, the 5 methods, that return the same result, but behave
different, depending on the query or for particular cases, the developer should
chose the proper one.

+------+----------+---------------------+---------------+---------------------------+
| Name | Normal   | Prepared Statements | Cache Normal  | Cache Prepared Statements |
+======+==========+=====================+===============+===========================+
| all  | GetAll   | PGetAll             | CacheGetAll   | CachePGetAll              |
+------+----------+---------------------+---------------+---------------------------+
| row  | GetRow   | PGetRow             | CacheGetRow   | CachePGetRow              |
+------+----------+---------------------+---------------+---------------------------+
| col  | GetCol   | PGetCol             | CacheGetCol   | CachePGetCol              |
+------+----------+---------------------+---------------+---------------------------+
| one  | GetOne   | PGetOne             | CacheGetOne   | CachePGetOne              |
+------+----------+---------------------+---------------+---------------------------+
| assoc| GetAssoc | PGetAssoc           | CacheGetAssoc | CachePGetAssoc            |
+------+----------+---------------------+---------------+---------------------------+


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
   database/X
   database/closeConnection
   database/connect
   database/csv
   database/debug
   database/forceTruncate
   database/getASSOC
   database/getAll
   database/getClientVersion
   database/getCol
   database/getColumnNames
   database/getNumOfFields
   database/getNumOfRows
   database/getNumOfRowsAffected
   database/getOne
   database/getRow
   database/getServerVersion
   database/isConnected
   database/map
   database/multipleInsert
   database/qstr
   database/query
   database/renumber
   database/useCache
