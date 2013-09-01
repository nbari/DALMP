<?php
require_once '../mplt.php';
$timer = new mplt();
require_once '../dalmp.php';
# -----------------------------------------------------------------------------------------------------------------

$db = new DALMP('utf8://dalmp:password@192.168.1.40:3306/dalmptest');

/**
  *
-- ----------------------------
--  Table structure for `dalmp_sessions`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dalmp_sessions` (
  `sid` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `data` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ref` varchar(255) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  KEY `index` (`ref`,`sid`,`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 *
 */

/**
 * to store sessions on mysql you need to pass the $db DALMP object if not defaults to sqlite
 */
$sessions = new DALMP_Sessions($db);

$GLOBALS['UID'] = 1;

if ((mt_rand() % 10) == 0) {
  $sessions->regenerate_id(4);
}

$_SESSION['test'] = 1 + @$_SESSION['test'];

$rs = $db->FetchMode('ASSOC')->PGetRow('SELECT * FROM dalmp_sessions WHERE ref=?', $GLOBALS['UID']);
print_r($rs);

echo $db->isCli(1),session_id().$db->isCli(1),$_SESSION['test'];

# -----------------------------------------------------------------------------------------------------------------
echo PHP_EOL,str_repeat('-', 80),PHP_EOL,'Time: ',$timer->getPageLoadTime(),' - Memory: ',$timer->getMemoryUsage(1),PHP_EOL,str_repeat('-', 80),PHP_EOL;
