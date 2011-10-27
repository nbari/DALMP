<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

/**
 *
 * For using DALMP with sqlite3 databases encrypted you 
 * need to install sqlcipher: http://sqlcipher.net/
 *
 * Encrypting your databases sessions or queues is helpful on shared environments where you need more privacy 
 *
 * -----------------------------------------------------------------------------------------------------------------
   On a Mac OS X you can compile sqlcipher with something like:
 ./configure --prefix=/usr/local/sqlcipher --enable-tempstore=yes CFLAGS="-DSQLITE_HAS_CODEC" LDFLAGS="-lcrypto" 
 
   On FreeBSD use this version:
   git clone git://github.com/nbari/sqlcipher.git

   Or try to use this port: http://www.freebsd.org/cgi/query-pr.cgi?pr=160993

   later you will have to recompile php sqlite3 extension 
 * -----------------------------------------------------------------------------------------------------------------
 * 
 */

/**
 *
 * For enable encryption you must define the constant DALMP_SQLITE_ENC_KEY
 * that constan will have the key used for encrypting / decrypting.
 * 
 * existing databases will need to be recreated, since now the full database will be encrypted 
 * in case of an error simple delete your old database and let DALMP re-create it automatically
 *
 */
define('DALMP_SQLITE_ENC_KEY', 'my sqlite key');

/**
 * thats all, now your sqlite3 database will be encrypted.
 */


define('DALMP_SESSIONS_SQLITE_DB','/tmp/sessions.db');

$sessions = new DALMP_Sessions();

/**
 * access this script from a browser for avoiding complains 
 * later check for the dalmp_sessions.db and try to read it
 * from the comand line you can test the encryption by doing
   strings dalmp_sessions.db
    ?:z{
    Eyq
    ...

 */
$_SESSION['test'] = 1 + @$_SESSION['test'];

echo $_SESSION['test'] .' - '.session_id();

echo DALMP::isCli(1);

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;

?>