DALMP - examples
================

It is recommended to run the examples in CLI mode, the only exception is when
testing sessions.

All examples use as user and password the environment variables:

  * MYSQL_USER
  * MYSQL_PASS
  * MYSQL_HOST
  * MYSQL_PORT

For example if using shell csh you can use something like:

    setenv MYSQL_USER dbadmin
    setenv MYSQL_PASS secret
    setenv MYSQL_HOST 192.168.1.30


while testing, on a terminal you may using something like this:

    mysqladmin -r -i 1 processlist -uroot -p