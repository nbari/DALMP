Example
=======

In this example the backend is going to be redis and the global reference name
will be 'UID', and the hash algorithim will be **sha512**.

For example, you can store in the reference, the current user id 'UID' and configure your site to only accept users to loggin once avoiding with this duplicate entries/access using the same user/password.

.. code-block:: php
   :linenos:

   <?php

   require_once 'dalmp.php';

   $cache= new DALMP\Cache\Redis('127.0.0.1', 6379);

   $handler = new DALMP\Sessions\Redis($cache, 'UID');

   $sessions = new DALMP\Sessions($handler, 'sha512');


   /**
    * your login logic goes here, for example suppose a user logins and has user id=37
    * therefore you store the user id on the globals UID.
    */
   $GLOBALS['UID'] = 37;

   /**
    * To check if there is no current user logged in you could use:
    */
   if ($sessions->getSessionRef($GLOBALS['UID'])) {
       // user is online
       exit('user already logged');
   } else {
       $sessions->regenerate_id(true);
   }


   /**
    * You can use $_SESSIONS like always
    */

    $_SESSIONS['foo'] = 'bar';
