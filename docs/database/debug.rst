debug
=====

This method will enable debugging, so that you can trace your full queries.

Parameters
..........

::

    debug($log2file = false, $debugFile = false)


:$log2file: When set to **1**, log is written to a single file, if **2** it creates multiple log files per request so that you can do a more intense debugging, 'off' stop debuging.

:$debugFile: Path of the file to write logs, defaults to ``/tmp/dalmp.log``


Example
.......