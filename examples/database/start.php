<?php
// Measure Page Load Time
require_once '../../MPLT.php';
$timer = new MPLT();
# ------------------------------------------------------------------------------

/**
 *
 * For using DALMP you need a Data Source Name (DSN)
 *
 * The DSN format is:
 *
 * charset://username:password@host:port/database
 *
 * or if using socket:
 *
 * charset://username:password@unix_socket=\path\of\the.socket/database
 *
 * Notice that the path of the socket is using backslashes.
 *
 * \path\of\the.socket will be translated to /path/of/the.socket
 *
 * If you want to use your system default charset, use 'mysql' as the charset.
 *
 * see bellow some examples.
 */

/**
 * require the DALMP class
 */
require_once '../../src/dalmp.php';

/**
 * example of a simple connection
 *
 * charset: default system
 * user: dalmp
 * password: password
 * host: 192.168.1.40
 * database: dalmptest
 *
 */
$db = new DALMP\Database('mysql://dalmp:password@192.168.1.40/dalmptest');
try {
  $rs = $db->getOne('SELECT now()');
} catch (\Exception $e) {
  print_r($e->getMessage());
}

/**
 * 1 log to single file
 * 2 log to multiple files (creates a log per request)
 * 'off' to stop debuging
 */
$db->debug(1);

echo $db, PHP_EOL; // print connection details

/**
 * example of a connection using UTF8 charset
 *
 * charset: utf8
 * user: dalmp
 * password: password
 * host: 127.0.0.1
 * port: 3306
 * database: dalmptest
 */
$db = new DALMP\Database('utf8://dalmp:password@127.0.0.1:3306/dalmptest');
try {
  $db->getOne('SELECT now()');
} catch (\Exception $e) {
  print_r($e->getMessage());
}

echo PHP_EOL, $db, PHP_EOL; // will print: DALMP :: connected to: db2, Character set: utf8, 127.0.0.1 via TCP/IP, Server version: ...

/**
 * example using SSL (OpenSSL support must be enabled for this to work)
 *
 * charset: latin1
 * user: root
 * password: mysql
 * host: 127.0.0.1
 * database: dalmp
 *
 * An array containing the SSL parameters must be passed as the second argument to the database method:
 *
 * $db = new DALMP(DSN, $ssl_array);
 *
 * key    = The path name to the key file.
 * cert   = The path name to the certificate file.
 * ca     = The path name to the certificate authority file.
 * capath = The pathname to a directory that contains trusted SSL CA certificates in PEM format.
 * cipher = A list of allowable ciphers to use for SSL encryption.
 *
 */
$ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);
$db = new DALMP\Database('latin1://root:mysql@127.0.0.1/dalmp', $ssl);

try {
  $db->getOne('SELECT NOW()');
  print_r($db->FetchMode('ASSOC')->GetRow("show variables like 'have_ssl'"));
} catch (\Exception $e) {
  print_r($e->getMessage());
}


/**
 * If you have SSL will get something like:
Array
(
  [Variable_name] => have_ssl
  [Value] => YES
)
* otherwise
*
Array
(
  [Variable_name] => have_ssl
  [Value] => DISABLED
)
 */

try {
  print_r($db->GetRow("show status like 'ssl_cipher'"));
} catch (\Exception $e) {
  print_r($e->getMessage());
}

/**
 * IF SSL working you should see something similar to this:
Array
(
  [Variable_name] => Ssl_cipher
  [Value] => DHE-RSA-AES256-SHA
)
* otherwise
Array
(
  [Variable_name] => Ssl_cipher
  [Value] =>
)
 */

/**
 * example using a socket for the connection
 *
 * charset: utf8
 * user: root
 * password: mysql
 * socket path: /tmp/mysql.sock
 * database: dalmp
 */
$db = new DALMP\Database('utf8://root:mysql@unix_socket=\tmp\mysql.sock/dalmp');
$db->debug(1);
try {
  echo PHP_EOL, 'example using unix_socket: ', $db->getOne('SELECT NOW()'), PHP_EOL;
} catch (\Exception $e) {
  print_r($e->getMessage());
}

echo $db; // will print: DALMP :: connected to: db4, Character set: utf8, Localhost via UNIX socket,...

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
