<?php
require_once '../../MPLT.php';
$timer = new MPLT();
require_once '../../src/dalmp.php';
# ------------------------------------------------------------------------------

$user = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASS') ?: '';
$host = getenv('MYSQL_HOST') ?: '127.0.0.1';
$port = getenv('MYSQL_PORT') ?: '3306';

$db = new DALMP\Database("utf8://$user:$password@$host:$port/dalmp");

/**
 *  load zone files to mysql
 *  mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root mysql
 */
$db->PExecute('SET time_zone=?','+00:00');

$db->FetchMode('ASSOC');

$sql = 'SELECT Name, Continent FROM Country WHERE Population > ? AND Code LIKE ? LIMIT ?';
$rs = $db->PGetAll($sql, 10000000, '%P%', 2);

print_r($rs);

$rs = $db->Execute('DROP TABLE IF EXISTS `tests`');
$rs = $db->Execute('CREATE TABLE `tests` (id INT(11) unsigned NOT NULL AUTO_INCREMENT, col1 varchar(255), col2 varchar(255), col3 varchar(255), status iNT(1), PRIMARY KEY (id))');
$rs = $db->AutoExecute('tests', array('col1' => 'ai eu', 'col2' => 2, 'status' => 0));

/**
 * status value is 0 or 1 on table
 * NOTICE the use of ===
 */
$sql = 'SELECT status FROM tests WHERE id=?';
$rs = $db->PgetOne($sql, 3);
if ($rs === false) {
    echo "no result".$timer->isCli(1);
} elseif ($rs == 0) {
    echo "$rs = 0".$timer->isCli(1);
} else {
    echo "$rs > 0".$timer->isCli(1);
}

/**
 * passing an array as an argument
 * helpful in cases where searching float values stored on varchar fields
 * $db->PGetAll($sql, array('s' => 99.3, 1));
 */
$sql = 'SELECT * FROM tests WHERE id=? AND col1=?';
$rs = $db->PGetAll($sql, array(3, 's' => 'string'));
var_dump($rs);

/**
 * using the Prepare method,
 * Useful when building dynamic queries that require prepared statements
 * The prepare method automatically detect the input type,
 * you can also override this, using something like: Prepare('s','1e1');
 * if no input it will return the array with the prepared statements
 */
$X = 3;
$id = 1;
$db->Prepare($id);
$sql = 'SELECT * FROM tests WHERE id=? ';
if ($X == 3) {
    $db->Prepare($X);
    $sql .= 'AND id !=? ';
}
$db->Prepare('s', 'ai eu');
$sql .= 'AND col1=?';

/**
 * this will produce a query like:
 * "sql: SELECT * FROM tests WHERE id=? AND id !=? AND col1=?" with params = ["iis",1,3,"ai eu"]
 */

echo "sql: $sql" , PHP_EOL;
echo 'Args: ', PHP_EOL;
print_r($db->Prepare());

$rs = $db->PgetAll($sql, $db->Prepare());
echo 'Result: ', print_r($rs), PHP_EOL;

/**
 * insert and get last_insert_id
 */
$db->PExecute('INSERT INTO tests (col1, col2) VALUES(?,?)', rand(), rand());
echo 'Last insert ID: ', $db->Insert_Id();

# ------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
