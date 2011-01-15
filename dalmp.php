<?php
/**
 * -----------------------------------------------------------------------------------------------------------------
 * DALMP - Database Abstraction Layer for MySQL using PHP
 * -----------------------------------------------------------------------------------------------------------------
 * for use you must define the following:
 *
 * define('DB_USERNAME', 'username');
 * define('DB_PASSWORD', 'password');
 * define('DB_HOST', 'localhost');
 * define('DB_PORT', 3306);
 * define('DB_DATABASE', 'database');
 * define('DB_CHARSET', 'utf8');
 * define('DB1_CNAME', 'db1');
 * define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB1_CNAME);
 * # optional
 * # define('MEMCACHE_HOSTS','127.0.0.1;192.168.0.1:11234');
 * # define('REDIS_HOST','127.0.0.1');
 * # define('REDIS_PORT', 6379);
 * # define('DALMP_CONNECT_TIMEOUT', 30);
 * # define('DALMP_SESSIONS_REF', 'UID');
 * # define('DALMP_SESSIONS_KEY', 'mykey');
 * # define('DALMP_SESSIONS_REDUNDANCY', true);
 * # define('DALMP_HTTP_CLIENT_CONNECT_TIMEOUT', 1);
 * # define('DALMP_DEBUG_FILE', '/tmp/dalmp/debug.log');
 * # define('DALMP_CACHE_DIR', '/tmp/dalmp/cache/');
 *
 * initialize the class:
 *
 * $db = DALMP::getInstance();
 * $db->database(DSN);
 * # if you want to use APC
 * # $db->Cache('apc');
 * # if you want to use memcache
 * # $db->Cache('memcache',MEMCACHE_HOSTS);
 * # if you want to use redis
 * # $db->Cache('redis',REDIS_HOST, REDIS_PORT);
 *
 *
-- ----------------------------
--  Table structure for `dalmp_sessions`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `dalmp_sessions` (
  `sid` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `data` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ref` int(11) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  KEY `index` (`ref`,`sid`,`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 *
 * -----------------------------------------------------------------------------------------------------------------
 * @link http://code.dalmp.com
 * @copyright Nicolas de Bari Embriz <nbari@dalmp.com>
 * @version 0.9.252
 * -----------------------------------------------------------------------------------------------------------------
 */
if (!defined('DALMP_DIR')) define('DALMP_DIR', dirname(__FILE__));

class DALMP {
	/**
	 * Contains the database instance.
	 *
	 * @access private
	 * @var instance
	 */
	private static $db_instance;

	/**
	 * Contains the database parameters DSN.
	 *
	 * @access private
	 * @var array
	 */

	private $dsn = array();
	/**
	 * Holds the fetchMode.
	 *
	 * @access private
	 * @var mixed
	 */
	private $fetchMode = MYSQLI_BOTH;

	/**
	 * Holds the num of rows returned.
	 *
	 * @access private
	 * @var int
	 */
	private $numOfRows = null;

	/**
	 * Holds the num of fields returned.
	 *
	 * @access private
	 * @var int
	 */
	private $numOfFields = null;

	/**
	 * Holds the connection name
	 *
	 * @access private
	 * @var mixed
	 */
	private $cname = null;

	/**
	 * connection timeout in seconds
	 *
	 * @access private
	 * @var int
	 */
	private $connect_timeout = 30;

	/**
	 * Contains database connection information.
	 *
	 * @access private
	 * @var array
	 */
	protected $_connection = array();

	/**
	 * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a result object.
	 * For other successful queries mysqli_query() will return TRUE.
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_rs = null;

	/**
	 * returns a statement object or FALSE if an error occurred.
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_stmt = null;

	/**
	 * Contains the allowed paramteres for the prepared statments
	 *
	 * @access protected
	 * @var array
	 */
	protected $_allowedParams = array('i','d','s','b');

	/**
	 * Contains the allowed cache options
	 * @access protected
	 * @var array
	 */
	protected $_cacheOptions = array('dir','apc','memcache','redis');

	/**
	 * Contains order of the cache types to use
	 * @access protected
	 * @var array
	 */
	protected $_cacheOrder = array('dir');

	/**
	 * Contain selected cache type
	 * @access protected
	 * @var string
	 */
	protected $_cacheType = 'dir';

	/**
	 * memcache hosts
	 *
	 * @access private
	 * @var array
	 */
	private $memCacheHosts = array();

	/**
	 * Use memcache compression
	 *
	 * @access private
	 * @var boolean
	 */
	private $memCacheCompress = 0;

	/**
	 * memcache connection
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_memcache = null;

	/**
	 * redis connection
	 *
	 * @access protected
	 * @var mixed
	 */
	protected $_redis = null;

	/**
	 * cache timeout in seconds, default to 1h
	 *
	 * @access private
	 * @var mixed
	 */
	private $timeout = 3600;

	/**
	 * transaction status
	 * @access private
	 * @var array
	 */
	private $trans = array();

	/**
	 *  connection name to use for storing sessions
	 *
	 * @access private
	 * @var mixed
	 */
	private $dalmp_sessions_cname = null;

	/**
	 * table to use for sessions
	 *
	 * @access private
	 * @var mixed
	 */
	private $dalmp_sessions_table = 'dalmp_sessions';

	/**
	 * use cache for sessions
	 * @access private
	 * @var boolean
	 */
	private $dalmp_sessions_cache = false;

	/**
	 * use cache type for sesssion
	 * @access private
	 * @var string
	 */
	private $dalmp_sessions_cache_type = null;

	/**
	 * sqlite database to use for queueing
	 *
	 * @access private
	 * @var mixed
	 */
	private $dalmp_queue_db = 'queue.db';

	/**
	 * sqlite database to use for queueing http requests
	 *
	 * @access private
	 * @var mixed
	 */
	private $dalmp_queue_url_db = 'queue_url.db';

	/**
	 * http_client connect_timeout  to use for queueing http requests
	 *
	 * @access private
	 * @var mixed
	 */
	private $http_client_connect_timeout = 3;

	/**
	 * If enabled, logs all queries and executions.
	 *
	 * @access private
	 * @var boolean
	 */
	private $debug = false;

	 /**
    * If enabled, logs all queries and executions for the sessions.
    *
    * @access private
    * @var boolean
    */
  private $debug2 = false;

	/**
	 * If enabled, logs all queries and executions for the sessions.
	 *
	 * @access private
	 * @var boolean
	 */
	private $debug_sessions = false;

	/**
	 * holds the time where debug begins
	 *
	 * @access private
	 * @var mixed
	 */
	private $debug_time_start = null;

	/**
	 * decimals used for printing the time
	 *
	 * @access private
	 * @var int
	 */
	private $debug_decimals = 4;

	/**
	 * write debug output to file
	 *
	 * @access private
	 * @var boolean
	 */
	private $debug2file = false;

	/**
	 * Contents the log
	 *
	 * @access private
	 * @var array
	 */
	private $log = array();

	/**
	 * start of class
	 */
	private function __construct() {
	}

	public static function getInstance() {
		if (!isset(self::$db_instance)) {
			$object = __CLASS__;
			self::$db_instance = new $object;
		}
		return self::$db_instance;
	}

	public function __clone() {
		throw new Exception('Cannot clone ' . __CLASS__ . ' class');
	}

	public function __destruct() {
		foreach (array_keys($this->_connection) as $cn) {
			$this->closeConnection($cn);
		}
		if ($this->debug) {
			$this->getLog();
		}
	}

	public function getLog() {
		if ($this->debug2file) {
			$debugFile = defined('DALMP_DEBUG_FILE') ? DALMP_DEBUG_FILE : DALMP_DIR . '.dalmp.log';
			$fh = fopen($debugFile, 'a+');
			$start = str_repeat('-', 80) . PHP_EOL;
			fwrite($fh, 'START ' . @date('r') . PHP_EOL);
			fwrite($fh, $start);
		} elseif($this->isCli()) {
			echo str_repeat('-', 80) . PHP_EOL;
			$hr = null;
		} else {
			echo '<div style="margin: 10px; font-size: 12px; font-family: monospace,serif; text-align: left; border-color: #FF7C0A; background-color: #FFF; color: #000; border-style: solid; border-width: 1px;">';
			$hr = '<hr style="border-top: 0px; border-left: 0px; border-right: 0px: border-bottom: 1px dashed #006ED2">';
		}

		$indent = strlen(count($this->log));
		foreach ($this->log as $key => $logs) {
			$spaces = str_repeat(' ', $indent - strlen($key));
			foreach ($logs as $etime => $log) {
				if ($this->debug2file) {
				  fwrite($fh, "$spaces$key - $etime - ".stripslashes($log) . PHP_EOL);
				} else {
					echo "$hr$spaces$key - $etime - ".stripslashes($log) . $this->isCli(1);
			  }
			}
		}

		if($this->debug2file) {
			fwrite($fh, $start);
			fwrite($fh, 'END ' . @date('r') . PHP_EOL);
			fwrite($fh, $start);
			fclose($fh);
		} elseif ($this->isCli()) {
				echo str_repeat('-', 80) . PHP_EOL;
		} else {
			echo '</div>';
		}
	}

	public function debug($log2file = null) {
		if($log2file) {
			$this->debug2file = true;
		}
		$this->debug = true;
		$this->debug_time_start = microtime(true);
	}

	public function debugSessions() {
		$this->debug_sessions = true;
		$this->debug2 = true;
		if (!$this->debug) {
			$this->debug();
		}
	}

	public function add2log() {
		$args = func_get_args();
		$key = array_shift($args);
		$method = is_array(reset($args)) ? json_encode(array_shift($args)) : array_shift($args);
		$log = empty($args) ? (empty($method) ? "[$key]" : "[$key - $method]") : "[$key - $method] -> ".json_encode($args);
		$etime = number_format(microtime(true) - $this->debug_time_start, $this->debug_decimals);
		$this->log[][$etime] = $log;
	}

	public function database($dsn = null) {
		if ($dsn) {
			if ($this->debug) { $this->add2log('DSN', $dsn); }
			$dsn = parse_url($dsn);
			$this->dsn['charset'] = isset($dsn['scheme']) ? rawurldecode($dsn['scheme']) : null;
			$this->dsn['host'] = isset($dsn['host']) ? rawurldecode($dsn['host']) : '127.0.0.1';
			$this->dsn['port'] = isset($dsn['port']) ? rawurldecode($dsn['port']) : 3306;
			$this->dsn['user'] = isset($dsn['user']) ? rawurldecode($dsn['user']) : null;
			$this->dsn['pass'] = isset($dsn['pass']) ? rawurldecode($dsn['pass']) : null;
			$this->dsn['dbName'] = isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : null;
			$this->dsn['cname'] = isset($dsn['query']) ? rawurldecode($dsn['query']) : $this->dsn['dbName'];
			if ($this->debug) { $this->add2log(__METHOD__, $this->dsn); }
			$this->_connect($this->dsn['cname']);
		} else {
			die("DSN missing: define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);");
		}
	}

	protected function _connect($cn) {
		if ($this->isConnected($cn)) {
			if ($this->debug) { $this->add2log(__METHOD__, $cn, 'still connected'); }
			return;
		}
		if (!extension_loaded('mysqli')) {
			die('The Mysqli extension is required');
		}
		$this->_connection[$cn] = mysqli_init();
		$timeout = defined('DALMP_CONNECT_TIMEOUT') ? DALMP_CONNECT_TIMEOUT : $this->connect_timeout;
		mysqli_options($this->_connection[$cn], MYSQLI_OPT_CONNECT_TIMEOUT, $timeout);
		$rs = mysqli_real_connect($this->_connection[$cn], $this->dsn['host'], $this->dsn['user'], $this->dsn['pass'], $this->dsn['dbName'], $this->dsn['port']);
		if ($rs === false || mysqli_connect_errno()) {
			if ($this->debug) { $this->add2log(__METHOD__, 'ERROR', 'mysqli connection error'); }
			$this->closeConnection($cn);
			throw new Exception(mysqli_connect_error() , mysqli_connect_errno());
		}
		mysqli_set_charset($this->_connection[$cn], $this->dsn['charset']);
		$this->cname = $cn;
		if ($this->debug) { $this->add2log(__METHOD__, "connected to: $cn", mysqli_get_host_info($this->_connection[$cn]) , 'protocol version: ' . mysqli_get_proto_info($this->_connection[$cn]) , 'character set: ' . mysqli_character_set_name($this->_connection[$cn]) , "connect timeout: $timeout"); }
	}

	public function isConnected($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		return empty($this->_connection[$cn]) ? false : ((bool)($this->_connection[$cn] instanceof mysqli));
	}

	public function closeConnection($cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, $cn); }
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->isConnected($cn)) {
			$this->_connection[$cn]->close();
		}
		$this->_connection[$cn] = null;
	}

	public function getConnection($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		$this->_connect($cn);
		return $this->_connection[$cn];
	}

	public function getNumOfRows() {
		return $this->numOfRows;
	}

	public function getNumOfFields() {
		return $this->numOfFields;
	}

	public function getColumnNames($table = null) {
		return ($table) ? $this->getCol("DESCRIBE $table") : false;
	}

	public function setFetchMode() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log(__METHOD__, $args); }
		return call_user_func_array(array($this, 'FetchMode'), $args);
	}

	public function FetchMode($mode = null) {
		switch (strtoupper($mode)) {
			case 'NUM':
				$this->fetchMode = MYSQLI_NUM;
				break;

			case 'ASSOC':
				$this->fetchMode = MYSQLI_ASSOC;
				break;

			default:
				$this->fetchMode = MYSQLI_BOTH;
				break;
		}
		if ($this->debug) { $this->add2log(__METHOD__, $mode, $this->fetchMode); }
	}

	public function PClose() {
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		$this->_stmt->free_result();
		$this->_stmt->close();
	}

	protected function _pFetch($get = null) {
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__, $get); }
		$meta = $this->_stmt->result_metadata();
		$columns = array();
		while ($column = $meta->fetch_field()) {
			// this is to stop a syntax error if a column name has a space in e.g "This Column"
			// http://php.net/manual/en/mysqli-stmt.fetch.php
			$columnName = str_replace(' ', '_', $column->name);
			$columns[$columnName] = &$$columnName;
		}
		call_user_func_array(array($this->_stmt,'bind_result'), $columns);
		$rs = array();
		switch ($get) {
			case 'one':
				while ($this->_stmt->fetch()) {
					$rs = array_shift($columns);
					break;
				}
				$rs = is_array($rs) ? reset($rs) : $rs;
				break;

			case 'col':
				while ($this->_stmt->fetch()) {
					$rs[] = reset($columns);
				}
				break;

			case 'assoc':
				if ($this->numOfFields < 2) {
					if ($this->debug) { $this->add2log('PreparedStatements',__METHOD__, 'ERROR', $get, 'num of columns < 2'); }
					return false;
				}
				if ($this->numOfFields == 2) {
					while ($this->_stmt->fetch()) {
						$rs[reset($columns)] = next($columns);
					}
				} else {
					while ($this->_stmt->fetch()) {
						$rs[reset($columns)] = array_slice($columns,1);
					}
				}
				break;

			default:
				while ($this->_stmt->fetch()) {
					$tmp = array();
					foreach ($columns as $key => $val) {
						switch ($this->fetchMode) {
							case MYSQLI_NUM:
								$tmp[] = $val;
								break;

							case MYSQLI_ASSOC:
								$tmp[$key] = $val;
								break;

							default:
								$tmp[] = $val;
								$tmp[$key] = $val;
								break;
						}
					}
					array_push($rs, $tmp);
					if ($get == 'row') {
						$rs = array_shift($rs);
						break;
					}
				}
				break;
		}
		unset($i);
		$this->PClose();
		return $rs;
	}

  /**
	 * Prepared Statements
	 * arguments: $sql, $params, $cn
	 * example: PGetAll('SELECT * FROM users WHERE name=? AND id=?', 'name', 1, 'db1')
	 * user also can define  the corresponding type of the bind variables (i, d, s, b): http://pt.php.net/manual/en/mysqli-stmt.bind-param.php
	 * example: PGetAll('SELECT * FROM table WHERE name=? AND id=?', array('s'=>'name', 'i'=>1), 'db1');
	 */
	public function PExecute() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__, $args); }
		$sql = array_shift($args);
		$cn = end($args);
		if (in_array($cn, array_keys($this->_connection))) {
			$cn = array_pop($args);
			$this->cname = $cn;
		} else {
			$cn = $this->cname;
		}
		$this->_stmt = $this->getConnection($cn)->prepare($sql);
		if (!$this->_stmt) {
			$this->closeConnection($cn);
			trigger_error('ERROR -> '. __METHOD__ .": Please check your sql statement, unable to prepare: $sql on $cn with args: ".json_encode($args)." available connections: " . implode(', ', array_keys($this->_connection)) , E_USER_ERROR);
		}
		$params = array();
		$types = null;
		$i = 0;
		$args = is_array(current($args)) ? current($args) : $args;
		if (!empty($args)) {
			foreach ($args as $key => $param) {
				if (is_int($key)) {
				  $params[] = &$args[$i];
				} else {
				  $params[] = &$args[$key];
				}
				if (!in_array($key, $this->_allowedParams, true)) {
					if (is_int($param)) {
						$key = 'i';
					} elseif (is_float($param)) {
						$key = 'd';
					} elseif (is_string($param)) {
						$key = 's';
					} else {
						$key = 'b';
					}
				}
				$types.= $key;
				$i++;
			}
			unset($i);
			array_unshift($params, $types);
			if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__, "sql: $sql params:", $params, "cn: $cn"); }
			call_user_func_array(array($this->_stmt,'bind_param'), $params);
		}
		/**
		 *  if you get erros like 'Illegal mix of collations (latin1_swedish_ci,IMPLICIT) and (utf8_general_ci,COERCIBLE)'
		 *  try to set your table fiels to:
		 *  "character set: UTF8"
		 *  and
		 *  "collation: utf8_unicode_ci"
		 */
		if ($this->_stmt->execute()) {
			$this->_stmt->store_result();
			$this->numOfRows = $this->_stmt->num_rows;
			$this->numOfFields = $this->_stmt->field_count;
			return true;
		} else {
			if(array_key_exists($cn, $this->trans)) {
			  $this->trans[$cn]['error']++;
			}
			if ($this->debug) { $this->add2log('PreparedStatements',  __METHOD__, 'ERROR',"sql: $sql cn: $cn params: ",$params," Errorcode:". $this->getConnection($cn)->errno); }
			trigger_error('ERROR -> '. __METHOD__ .": sql: $sql on $cn with params: ".json_encode($params).' - '.$this->getConnection($cn)->error, E_USER_NOTICE);
			return false;
		}
	}

	public function PgetAll() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		call_user_func_array(array($this,'PExecute'), $args);
		return $this->_pFetch('all');
	}

	public function PgetRow() {
		$args = func_get_args();
    if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		call_user_func_array(array($this,'PExecute'), $args);
		return $this->_pFetch('row');
	}

	public function PgetCol() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		call_user_func_array(array($this,'PExecute'), $args);
		return $this->_pFetch('col');
	}

	public function PgetOne() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		call_user_func_array(array($this,'PExecute'), $args);
		return $this->_pFetch('one');
	}
	public function PgetASSOC() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('PreparedStatements', __METHOD__); }
		call_user_func_array(array($this,'PExecute'), $args);
		return $this->_pFetch('assoc');
	}

	public function Insert_Id() {
		if ($this->debug) { $this->add2log( __METHOD__); }
		return mysqli_insert_id($this->getConnection());
	}

	public function AutoExecute($table = null, $fields = null, $mode = 'INSERT', $where = null, $cn = null) {
		if (!$table || !is_array($fields)) {
			if ($this->debug) { $this->add2log(__METHOD__, 'ERROR', 'either table or fields missing'); }
			return false;
		}
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->debug) { $this->add2log(__METHOD__, 'args:', $table, $fields, $mode, $where, $cn); }
		$mode = strtoupper($mode);
		if ($mode == 'UPDATE' && !$where) {
			if ($this->debug) { $this->add2log( __METHOD__, 'ERROR', 'WHERE clause missing'); }
			return false;
		}
		if ($columnNames = $this->getColumnNames($table)) {
			$data = array();
			$placeholder = '';
			foreach ($columnNames as $col) {
				if (isset($fields[$col])) {
					$data["`$col`"] = $fields[$col];
					$placeholder.= '?,';
				}
			}
			if (count($data) < 1) {
				if ($this->debug) { $this->add2log(__METHOD__, 'ERROR', "no matching fields on table: $table with fields:", $fields); }
				return false;
			}
		} else {
			return false;
		}
		switch ($mode) {
			case 'INSERT':
				$fields = implode(', ', array_keys($data));
				$placeholder = rtrim($placeholder, ',');
				$query = array_values($data);
				$sql = "INSERT INTO $table ($fields) VALUES($placeholder)";
				array_unshift($query, $sql);
				$query[] = $cn;
				return call_user_func_array(array($this,'PExecute'), $query);
				break;

			case 'UPDATE':
				$fields = implode('=?, ', array_keys($data));
				$fields.= '=?';
				$query = array_values($data);
				$sql = "UPDATE $table SET $fields WHERE $where";
				array_unshift($query, $sql);
				$query[] = $cn;
				return call_user_func_array(array($this,'PExecute'), $query);
				break;

			default:
				if ($this->debug) { $this->add2log(__METHOD__, 'ERROR', 'mode must be INSERT or UPDATE'); }
				return false;
				break;
		}
	}

	public function ErrorMsg($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		return $this->getConnection($cn)->error;
	}

	public function qstr($value, $cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->debug) { $this->add2log(__METHOD__, "$value cn: $cn"); }
		if (get_magic_quotes_gpc()) {
			$rs = stripslashes($value);
		} else {
			if (is_int($value) || is_float($value)) {
				$rs = $value;
			} else {
				$rs = $this->getConnection($cn)->real_escape_string($value);
			}
		}
		if ($this->debug) { $this->add2log(__METHOD__, "returned: $rs"); }
		return $rs;
	}

	protected function _fetch() {
		if ($this->debug) {$this->add2log(__METHOD__); }
		return $this->_rs->fetch_array($this->fetchMode);
	}

	public function Close() {
		if ($this->debug) { $this->add2log(__METHOD__); }
		$this->_rs->close();
	}

	public function Execute($sql, $cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn: $cn"); }
		if ($rs = $this->getConnection($cn)->query($sql)) {
			if (is_object($rs)) {
				$this->_rs = $rs;
				$this->numOfRows = $this->_rs->num_rows;
				$this->numOfFields = $this->_rs->field_count;
				if ($this->debug) { $this->add2log(__METHOD__, 'returned object', "#rows: $this->numOfRows #fields: $this->numOfFields"); }
				if(!$this->numOfRows) {
					$this->Close();
					return false;
				}
			}
			return (@$this->getConnection($cn)->affected_rows > 0 || $rs) ? true : false;
		} else {
			if(array_key_exists($cn, $this->trans)) {
			  $this->trans[$cn]['error']++;
			}
			if ($this->debug) { $this->add2log(__METHOD__, 'ERROR', "sql: $sql cn: $cn Errorcode: ".$this->getConnection($cn)->errno); }
			trigger_error('ERROR -> '. __METHOD__." : sql: $sql cn: $cn ".' - '.$this->getConnection($cn)->error, E_USER_NOTICE);
			return false;
		}
	}

	public function FetchRow() {
		if ($this->debug) { $this->add2log(__METHOD__); }
		return $this->_fetch();
	}

	public function getAll($sql, $cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn: $cn"); }
		if ($this->Execute($sql, $cn)) {
			$rows = array();
			while ($row = $this->_fetch()) {
				$rows[] = $row;
			}
			$this->Close();
			return $rows;
		} else {
			return false;
		}
	}

	public function getRow($sql, $cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn; $cn");}
		if ($this->Execute($sql, $cn)) {
			$row = $this->_fetch();
			$this->Close();
			return $row;
		} else {
			return false;
		}
	}

	public function getCol($sql, $cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn: $cn"); }
		if ($this->Execute($sql, $cn)) {
			$col = array();
			while ($row = $this->_rs->fetch_row()) {
				$col[] = reset($row);
			}
			$this->Close();
			return $col;
		} else {
			return false;
		}
	}

	public function getOne($sql, $cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn: $cn"); }
		if ($this->Execute($sql, $cn)) {
			$field = reset($this->_rs->fetch_row());
			$this->Close();
			return $field;
		} else {
			return false;
		}
	}

	public function getASSOC($sql, $cn = null) {
		if ($this->debug) { $this->add2log(__METHOD__, "sql: $sql cn: $cn"); }
		if ($this->Execute($sql, $cn)) {
			$cols = $this->numOfFields;
			if ($cols < 2) {
				return false;
			}
			$this->fetchMode = MYSQLI_ASSOC;
			$assoc = array();
			if ($cols == 2) {
				while ($row = $this->_fetch()) {
					$assoc[reset($row) ] = next($row);
				}
			} else {
				while ($row = $this->_fetch()) {
					$assoc[reset($row) ] = array_slice($row, 1);
				}
			}
			$this->Close();
			return $assoc;
		} else {
			return false;
		}
	}

	public function StartTrans($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		if(array_key_exists($cn, $this->trans)) {
			$this->trans[$cn]['level']++;
		  if ($this->debug) { $this->add2log('transactions',__METHOD__,'transaction level: '.$this->trans[$cn]['level']." cn: $cn"); }
			return $this->Execute('SAVEPOINT level'.$this->trans[$cn]['level'], $cn);
		} else {
			if ($this->debug) { $this->add2log('transactions',__METHOD__,"cn: $cn");}
			$this->trans[$cn] = array('level' => 0, 'error' => 0);
			return $this->Execute('BEGIN', $cn);
		}
	}

	public function CompleteTrans($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->debug) { $this->add2log('transactions',__METHOD__,"cn: $cn"); }
		if(array_key_exists($cn, $this->trans)) {
			if($this->trans[$cn]['error'] > 0) {
				if ($this->debug) { $this->add2log('transactions',__METHOD__,'ERROR','error in level: '.$this->trans[$cn]['level']." cn: $cn"); }
				if($this->trans[$cn]['level'] > 0) {
					$this->Execute('ROLLBACK TO SAVEPOINT level'.$this->trans[$cn]['level'], $cn);
					$this->trans[$cn]['level']--;
				} else {
					$this->Execute('ROLLBACK',$cn);
				}
				return false;
			}
			if($this->trans[$cn]['level'] == 0) {
				unset($this->trans[$cn]);
				return $this->Execute('COMMIT', $cn);
			} else {
				$rs = $this->Execute('RELEASE SAVEPOINT level'.$this->trans[$cn]['level'], $cn);
				$this->trans[$cn]['level']--;
				return $rs;
			}
		} else {
			return false;
		}
	}

	public function RollBackTrans($cn=null) {
		$cn = isset($cn) ? $cn : $this->cname;
		if ($this->debug) { $this->add2log('transactions',__METHOD__,"cn: $cn"); }
		if ($this->trans[$cn]['level']  > 0) {
			$rs = $this->Execute('ROLLBACK TO SAVEPOINT level'.$this->trans[$cn]['level'], $cn);
		 	$this->trans[$cn]['level']--;
		  return $rs;
		} else {
			return $this->Execute('ROLLBACK',$cn);
		}
	}

	public function memCache($hosts, $compress = false) {
		if ($this->debug) { $this->add2log('memCache', __METHOD__, "hosts: $hosts compress: $compress"); }
		if (!extension_loaded('memcache')) {
			if ($this->debug) { $this->add2log('memCache', __METHOD__, 'ERROR', 'Memcache PECL extension not loaded! - http://pecl.php.net/package/memcache'); }
			trigger_error('ERROR -> '. __METHOD__.': Memcache PECL extension not loaded! - http://pecl.php.net/package/memcache', E_USER_NOTICE);
			return false;
		}
		$memcache = new MemCache;
		$hosts = explode(';', $hosts);
		$this->_memcache = $memcache;
		$this->memCacheHosts = $hosts;
		$this->memCacheCompress = $compress ? MEMCACHE_COMPRESSED : 0;
		return $this->_memCacheConnect();
	}

	protected function _memCacheConnect() {
		$linkerror = array();
		foreach ($this->memCacheHosts as $hosts) {
			$out = explode(':', $hosts);
			$host = trim($out[0]);
		  if(strstr($host,'/')) {
				$port = 0;
				if ($this->debug) { $this->add2log('memCache', __METHOD__, "adding server via socket: $host"); }
				$host = "unix://$host";
		  } else {
				$port = isset($out[1]) ? trim($out[1]) : 11211;
				if ($this->debug) { $this->add2log('memCache', __METHOD__, "adding server via host: $host port: $port"); }
			}
			$this->_memcache->addServer($host, $port, true);
		}

		if (!@$this->_memcache->getVersion()) {
			if ($this->debug) { $this->add2log('memCache', __METHOD__, 'ERROR', 'Can not connect to any memcache server'); }
			$this->_memcache = false;
			return false;
		} else {
			return true;
		}
	}

	public function isMemcacheConnected() {
		return ((bool)($this->_memcache instanceof MemCache));
	}

	public function memCacheStats() {
		return $this->isMemcacheConnected() ? $this->_memcache->getExtendedStats() : false;
	}

	public function apcCache() {
		if (!extension_loaded('apc') && !ini_get('apc.enabled')) {
			if ($this->debug) { $this->add2log('apcCache', __METHOD__, 'ERROR', 'APC PECL extension is not loaded or enabled! - http://pecl.php.net/package/APC'); }
			trigger_error('ERROR -> '. __METHOD__.': APC PECL extension not loaded or enabled!', E_USER_NOTICE);
			return false;
		}
		return true;
	}

	public function apcStats() {
		return $this->apcCache() ? apc_cache_info() : false;
	}

	public function redisCache($host, $port, $timeout) {
		if (!extension_loaded('redis')) {
			if ($this->debug) { $this->add2log('redis', __METHOD__, 'ERROR', 'recis extension not loaded! - http://github.com/owlient/phpredis'); }
			trigger_error('ERROR ->'. __METHOD__.': redix extension not loaded! - http://github.com/owlient/phpredis', E_USER_NOTICE);
			return false;
		}
		$redis = new Redis();
		$this->_redis = $redis;
		$timeout = isset($timeout) ? $timeout : 1;
		try {
			if($this->debug) { $this->add2log('redis', __METHOD__,"connecting to $host port: $port timeout: $timeout"); }
			return $this->_redis->connect($host, $port, $timeout);
		} catch (RedisException $e) {
			if($this->debug) { $this->add2log('redis', __METHOD__,'ERROR',$e->getMessage()); }
		}
	}

	public function isRedisCacheConnected() {
		return ((bool)($this->_redis instanceof Redis));
	}

	public function redisStats() {
		return $this->isRedisCacheConnected() ? $this->_redis->info() : false;
	}

	public function redisSelect($db=null) {
		if($this->isRedisCacheConnected() && is_numeric($db)) {
			if ($this->debug) { $this->add2log('redis', __METHOD__, "select database: $db"); }
			$rs =  $this->_redis->select($db) ? true : false;
			if ($this->debug) {
				if(!$rs) { $this->add2log('redis', __METHOD__,"could not set database: $db"); }
			}
			return $rs;
		} else {
			if ($this->debug) { $this->add2log('redis', __METHOD__, "redis daemon not running, responding or db not set. db: $db"); }
			return false;
		}
	}

	public function redisDBSize() {
		return $this->isRedisCacheConnected() ? $this->_redis->dbSize() : false;
	}

	public function Cache($cache=null, $arg1=null, $arg2=null, $arg3=null) {
		$cache = isset($cache) ? $cache : 'dir';
		if(in_array(strtolower($cache),$this->_cacheOptions)) {
			array_unshift($this->_cacheOrder, $cache);
			if ($this->debug) { $this->add2log('Cache', __METHOD__, "selected Cache: $cache"); }
			switch(strtolower($cache)){
				case 'apc':
					return $this->apcCache();
					break;
				case 'memcache':
					return $this->memCache($arg1, $arg2);
					break;
				case 'redis':
					return $this->redisCache($arg1, $arg2, $arg3);
					break;
			}
		} else {
			if ($this->debug) { $this->add2log('Cache', __METHOD__, 'ERROR', "selected Cache: $cache not available"); }
			return false;
		}
	}

	public function Caches() {
		return $this->_cacheOrder;
	}

	/**
	 * setCache arguments: $sql, $object, $timeout, $key, $cn (connection name), $dc (use dir cache), $ct (cache type)
	 */
	public function setCache($sql, $object, $timeout, $key, $cn, $dc=true, $ct=null) {
		$hkey = sha1('DALMP' . $sql . $key . $cn);
		$ct = isset($ct) ? $ct : $this->_cacheType;
		if ($this->debug) { $this->add2log('Cache', __METHOD__, "hkey: $hkey sql: $sql timeout: $timeout key: $key cn: $cn dc: $dc object: ",$object); }
		$rs = false;
		switch($ct) {
			case 'apc':
				$rs = $this->apcCache() ? apc_store($hkey, $object, $timeout) : false;
				if ($this->debug) {
					$this->add2log('Cache','APC',"STORE key: $hkey timeout: $timeout object:",$object);
				  if(!$rs) {
						$this->add2log('Cache','APC','APC not responding');
					}
				}
				break;
			case 'memcache':
				$rs = $this->isMemcacheConnected() ? $this->_memcache->set($hkey, $object, $this->memCacheCompress, $timeout) : false;
				if ($this->debug) {
					$this->add2log('Cache','memCache',"SET key: $hkey compress: $this->memCacheCompress timeout: $timeout, object: ",$object);
				  if(!$rs) {
						$this->add2log('Cache','memCache',"memcache daemon not running or responding. rs: $rs");
					}
				}
				break;
			case 'redis':
				if ($timeout == 0 || $timeout == -1) {
					$rs = $this->isRedisCacheConnected() ? $this->_redis->set($hkey, serialize($object)) : false;
				} else {
					$rs = $this->isRedisCacheConnected() ? $this->_redis->setex($hkey, $timeout, serialize($object)) : false;
				}
				if ($this->debug) {
					$this->add2log('Cache','redis',"SET key: $hkey timeout: $timeout object:", $object);
				  if(!$rs) {
						$this->add2log('Cache','redis',"redis daemon not running or responding. rs: $rs");
					}
				}
				break;
		}
		if(!$rs && $dc) {
			if ($this->debug && $this->_cacheType != 'dir') {
				$this->add2log('Cache', 'ERROR',"[$this->_cacheType] not responding using dir cache");
			}
			$dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
			$dalmp_cache_dir = $dalmp_cache_dir.'/'.substr($hkey,0,2);
			if ($this->debug) { $this->add2log('Cache','dirCache', "using dir cache: $dalmp_cache_dir"); }
			if(!file_exists($dalmp_cache_dir)) {
				if(!mkdir($dalmp_cache_dir,0750,true)) {
					if ($this->debug) { $this->add2log('Cache','dirCache','ERROR',"Cannot create: $dalmp_cache_dir"); }
					trigger_error('ERROR -> '. __METHOD__ . ": dirCache - Cannot create: $dalmp_cache_dir", E_USER_NOTICE);
					return false;
				}
			}
			$cache_file = "$dalmp_cache_dir/dalmp_$hkey.cache";
			if (!($fp = fopen($cache_file, 'w'))) {
				if ($this->debug) { $this->add2log('Cache','dirCache','ERROR',"Cannot create cache file: $cache_file"); }
				trigger_error('ERROR -> '. __METHOD__. ": dirCache - Cannot create cache file $cache_file", E_USER_NOTICE);
				return false;
			}
			if (flock($fp, LOCK_EX) && ftruncate($fp, 0)) {
				if (fwrite($fp, serialize($object))) {
					flock($fp, LOCK_UN);
					fclose($fp);
					chmod($cache_file,0644);
					$time = time() + $timeout;
					touch($cache_file,$time);
					if ($this->debug) { $this->add2log('Cache','dirCache',"cache created: $cache_file, timeout: $timeout, time: $time"); }
					return true;
				} else {
					if ($this->debug) { $this->add2log('Cache','dirCache','ERROR',"Cannot write cache to file: $cache_file"); }
					return false;
				}
			} else {
				if ($this->debug) { $this->add2log('Cache','dirCache','ERROR',"Cannot lock/truncate the cache file: $cache_file"); }
				trigger_error('ERROR -> '. __METHOD__. ": dirCache Cannot lock/truncate the cache file: $cache_file", E_USER_NOTICE);
				return false;
			}
		} else {
			if ($this->debug) { $this->add2log('Cache', __METHOD__, "cache type: $ct, cache for hkey $hkey returned: $rs"); }
			return $rs;
		}
	}
	/**
	 * setCache arguments: $sql, $key, $cn (connection name), $dc (use dir cache), $ct (cache type)
	 */
	public function getCache($sql, $key, $cn, $dc=true, $ct=null) {
		$hkey = sha1('DALMP' . $sql . $key. $cn);
		$ct = isset($ct) ? $ct : $this->_cacheType;
		if ($this->debug) { $this->add2log('Cache', __METHOD__, "hkey: $hkey, sql: $sql, key: $key, cn: $cn, dc: $dc"); }
		$rs = false;
		switch($ct) {
			case 'apc':
				$rs = $this->apcCache() ? apc_fetch($hkey) : false;
				break;
			case 'memcache':
				$rs = $this->isMemcacheConnected() ? $this->_memcache->get($hkey) : false;
				break;
			case 'redis':
				$rs = $this->isRedisCacheConnected() ? unserialize($this->_redis->get($hkey)) : false;
				break;
		}
		if(!$rs && $dc) {
			if ($this->debug && $this->_cacheType != 'dir') {
				$this->add2log('Cache', $this->_cacheType, "no cache found for key: $hkey, using dir cache");
			}
			$dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
			$dalmp_cache_dir = $dalmp_cache_dir.'/'.substr($hkey,0,2);
			$cache_file = "$dalmp_cache_dir/dalmp_$hkey.cache";
			$content = @file_get_contents($cache_file);
			if ($content) {
		    $cache = unserialize(file_get_contents($cache_file));
			  $time = time();
			  $cache_time= filemtime($cache_file);
				$life = $cache_time - $time;
			  if($life > 0) {
				  if ($this->debug) { $this->add2log('Cache', 'dirCache', "returned cache from file: $cache_file, time: $time, cache_time: $cache_time, life: $life"); }
				  return $cache;
			  } else {
				  if ($this->debug) { $this->add2log('Cache', 'dirCache', "cache expired, deleting $cache_file"); }
				  @unlink($cache_file);
				  return false;
			  }
			} else {
				if ($this->debug) { $this->add2log('Cache', 'dirCache', 'ERROR',"no cached records, file: $cache_file"); }
				return false;
			}
		} else {
			if ($this->debug) { $this->add2log('Cache', __METHOD__, "cache type: $ct, cache retrieved for hkey $hkey"); }
			return $rs;
		}
	}

	public function CacheFlush($sql=null, $key=null, $cn=null, $cache=null) {
		if ($sql) {
			$cn = isset($cn) ? $cn : $this->cname;
		  $hkey = sha1('DALMP' . $sql . $key . $cn);
			if ($this->debug) { $this->add2log('Cache', __METHOD__, "flush hkey: $hkey, sql: $sql, key: $key, cn: $cn for: ". (isset($cache) ? $cache : implode(', ', $this->_cacheOrder))); }

			$this->_cacheOrder = in_array($cache, $this->_cacheOrder) ? array($cache) : $this->_cacheOrder;

			foreach($this->_cacheOrder as $value) {
				switch($value) {
					case 'apc':
						$rs = apc_delete($hkey);
						if ($this->debug) {
							$this->add2log('Cache', 'APC', "hkey: $hkey");
							if(!$rs) {
								$this->add2log('Cache', 'APC', 'ERROR', "could not flush khey: $hkey");
							}
						}
						break;
					case 'memcache':
						$rs = $this->isMemcacheConnected() ? $this->_memcache->delete($hkey) : false;
						if ($this->debug) {
							$this->add2log('Cache', 'memcache', "hkey: $hkey");
							if(!$rs) { $this->add2log('Cache','memcache','ERROR',"could not delete hkey: $hkey"); }
						}
						break;
					case 'redis':
						$rs = $this->isRedisCacheConnected() ? $this->_redis->delete($hkey) : false;
						if ($this->debug) {
							$this->add2log('Cache', 'redis', "hkey: $hkey");
							if(!$rs) {
								$this->add2log('Cache','redis','ERROR',"could not delete hkey: $hkey");
							}
						}
						break;
					case 'dir':
						$dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
						$dalmp_cache_dir = $dalmp_cache_dir.'/'.substr($hkey,0,2);
						$cache_file = "$dalmp_cache_dir/dalmp_$hkey.cache";
						if ($this->debug) { $this->add2log('Cache', 'dirCache', "hkey: $hkey"); }
						$rs = (isset($rs)) ? $rs : @unlink($cache_file);
						break;
				}
			}
			return $rs;
		} else {
			if ($this->debug) { $this->add2log('Cache', __METHOD__, 'flush all for: '.implode(', ',$this->_cacheOrder)); }

			foreach($this->_cacheOrder as $value) {
				switch($value) {
					case 'apc':
						$rs = apc_clear_cache('user');
						if ($this->debug) {
							$this->add2log('Cache', 'APC', "flush all");
						  if(!$rs) {
								$this->add2log('Cache','APC','ERROR','cannot flush all');
							}
						}
						break;
					case 'memcache':
						$rs = $this->isMemcacheConnected() ? $this->_memcache->flush() : false;
						if ($this->debug) {
							$this->add2log('Cache', 'memcache', "flush all");
						  if(!$rs) {
								$this->add2log('Cache','memcache','ERROR','cannot flush all');
							}
						}
						break;
					case 'redis':
						$rs = $this->isRedisCacheConnected() ? $this->_redis->flushDB() : false;
						if ($this->debug) {
							$this->add2log('Cache', 'redis', "flush db");
						  if(!$rs) {
								$this->add2log('Cache','redis','ERROR','cannot flush db');
							}
						}
						break;
					case 'dir':
						$dalmp_cache_dir = defined('DALMP_CACHE_DIR') ? DALMP_CACHE_DIR : '/tmp/dalmp';
						$rs = $this->_dirFlush($dalmp_cache_dir);
						if ($this->debug) { $this->add2log('Cache','dirCache', "flush all: $dalmp_cache_dir"); }
						break;
				}
			}
			return $rs;
		}
	}

	private function _dirFlush($dir, $kill_top_level = false) {
    if(!$dh = @opendir($dir)) {
			return;
		}
    while (($obj = readdir($dh)) !==false) {
      if( $obj === '.' || $obj === '..') {
				continue;
			}
      $x = $dir.'/'.$obj;

      if (strpos($obj,'.cache')) {
				@unlink($x);
			}
      if (is_dir($x)) $this->_dirFlush($x, true);
    }
    if ($kill_top_level === true) {
			@rmdir($dir);
		}
    return true;
  }

	/**
	 * general method for caching
	 * args: $method, $cachetype, $timeout, $sql, $key, $cn
	 */
	protected function _Cache() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('Cache', __METHOD__, 'args: ', $args); }
		$fetch = array_shift($args);
		$cachetype = reset($args);
		$cachetype = in_array(strtolower($cachetype), $this->_cacheOrder) ? array_shift($args) : reset($this->_cacheOrder);
		$this->_cacheType = $cachetype;
		$timeout = reset($args);
		$timeout = (!is_numeric($timeout)) ? $this->timeout: array_shift($args);
		$sql = array_shift($args);
		$key = isset($args[0]) ? $args[0] : $fetch;
		$cn = isset($args[1]) ? $args[1] : $this->cname;
		if ($this->debug) { $this->add2log('Cache', __METHOD__, "method: $fetch cachetype: $cachetype timeout: $timeout sql: $sql key: $key cn: $cn"); }
 		if ($cache = $this->getCache($sql, $key, $cn)) {
			return $cache;
		} else {
			switch($fetch) {
				case 'Execute':
				case 'getAll':
					$cache = $this->getAll($sql, $cn);
					break;
				case 'getRow':
					$cache = $this->getRow($sql, $cn);
					break;
				case 'getCol':
					$cache = $this->getCol($sql, $cn);
					break;
				case 'getOne':
					$cache = $this->getOne($sql, $cn);
					break;
				case 'getAssoc':
					$cache = $this->getASSOC($sql, $cn);
					break;
			}
			if (!$this->setCache($sql, $cache, $timeout, $key, $cn)) {
				if ($this->debug) { $this->add2log('Cache', __METHOD__, 'ERROR', "setCache: Error saving data to cache sql: $sql, cache: ", $cache," timeout: $timeout, key: $key, cn: $cn"); }
				trigger_error('ERROR -> '. __METHOD__ ." setCache: Error saving data to cache.", E_USER_NOTICE);
			}
			return $cache;
		}
	}

	public function CacheExecute() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
		$args = func_get_args();
		array_unshift($args, 'Execute');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	public function CacheGetAll() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
		$args = func_get_args();
		array_unshift($args, 'getAll');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	public function CacheGetRow() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
 		$args = func_get_args();
		array_unshift($args, 'getRow');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	public function CacheGetCol() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
 		$args = func_get_args();
		array_unshift($args, 'getCol');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	public function CacheGetOne() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
 		$args = func_get_args();
		array_unshift($args, 'getOne');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	public function CacheGetASSOC() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__); }
		$args = func_get_args();
		array_unshift($args, 'getAssoc');
		return call_user_func_array(array($this,'_Cache'), $args);
	}

	/**
	 * general method for caching PreparedStatements
	 * args: $method, $cachetype, $timeout, $sql, $key, $cn
	 */
	protected function _CacheP() {
		$args = func_get_args();
		if ($this->debug) { $this->add2log('Cache', __METHOD__, 'PreparedStatements args: ', $args); }
		$fetch = array_shift($args);
		$cachetype = reset($args);
		$cachetype = in_array(strtolower($cachetype),$this->_cacheOrder) ? array_shift($args) : reset($this->_cacheOrder);
		$this->_cacheType = $cachetype;
		$timeout = reset($args);
		$timeout = (!is_numeric($timeout)) ? $this->timeout : array_shift($args);
		$sql = array_shift($args);
		$cn = end($args);
		if (in_array($cn, array_keys($this->_connection))) {
			$cn = array_pop($args);
			$this->cname = $cn;
		} else {
			$cn = $this->cname;
		}
		// expected params
		$eparams = count(explode('?',$sql,-1));
		$args = is_array(current($args)) ? current($args) : $args;
		if(count($args) > $eparams) {
			$key = array_pop($args);
			$params = $args;
		} else {
			$key = $fetch.implode('|',array_merge(array_keys($args),$args));
			$params = $args;
		}
		array_unshift($args, $sql);
		array_push($args, $cn);
		if ($this->debug) { $this->add2log('Cache', __METHOD__, "PreparedStatements method: $fetch, cachetype: $cachetype, timeout: $timeout, sql: $sql, params: ".implode('|',$params).", key: $key, cn: $cn"); }
		if ($cache = $this->getCache($sql, $key, $cn)) {
			return $cache;
		} else {
			if ($this->debug) { $this->add2log('Cache', __METHOD__, 'PreparedStatements no cache returned, executing query PExecute with args: ',$args); }
			$nargs = array();
			foreach (array_keys($args) as $akey) {
				if (!is_int($akey)) {
					$nargs['dalmp'][$akey] = $args[$akey];
				} else {
					$nargs[] = $args[$akey];
				}
			}
			call_user_func_array(array($this,'PExecute'), $nargs);
			$cache = $this->_pFetch($fetch);
			if (!$this->setCache($sql, $cache, $timeout, $key, $cn)) {
				if ($this->debug) { $this->add2log('Cache', __METHOD__, 'ERROR', "setCache: Error saving data to cache sql: $sql, params: ".implode('|',$params).", cache: ",$cache," timeout: $timeout, key: $key, cn: $cn"); }
				trigger_error('ERROR -> '. __METHOD__ ." setCache: Error saving data to cache.", E_USER_NOTICE);
			}
			return $cache;
		}
	}

	public function CachePgetAll() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__,'PreparedStatements'); }
		$args = func_get_args();
		array_unshift($args, 'all');
		return call_user_func_array(array($this,'_CacheP'), $args);
	}

	public function CachePgetRow() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__,'PreparedStatements'); }
		$args = func_get_args();
		array_unshift($args, 'row');
		return call_user_func_array(array($this,'_CacheP'), $args);
	}

	public function CachePgetCol() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__,'PreparedStatements'); }
		$args = func_get_args();
		array_unshift($args, 'col');
		return call_user_func_array(array($this,'_CacheP'), $args);
	}

	public function CachePgetOne() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__,'PreparedStatements'); }
		$args = func_get_args();
		array_unshift($args, 'one');
		return call_user_func_array(array($this,'_CacheP'), $args);
	}

	public function CachePgetASSOC() {
		if ($this->debug) { $this->add2log('Cache', __METHOD__,'PreparedStatements'); }
		$args = func_get_args();
		array_unshift($args, 'assoc');
		return call_user_func_array(array($this,'_CacheP'), $args);
	}

	public function getSessionsRefs() {
		$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
		$sessions_key = 'DALMP_SESSIONS_REF'.$key;
		if ($this->debug) { $this->add2log('sessions', __METHOD__, $sessions_key); }
		$cached_refs = $this->getCache('sessions_ref', $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
		$db_refs = $this->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
		$dba = array();
		if ($db_refs) {
			foreach ($db_refs as $value) {
				$dba[$value['sid']] = array($value['ref'] => $value['expiry']);
			}
		}
		return array_merge($dba, (is_array($cached_refs) ? $cached_refs : array())); // give priority to cache
	}

	public function getSessionRef($ref) {
		$refs = $this->getSessionsRefs();
		if ($this->debug) { $this->add2log('sessions', __METHOD__, "ref: $ref, refs:", $refs); }
		$rs = array();
		foreach ($refs as $key => $expiry) {
		  if (key($expiry) == $ref) {
				$rs[$key] = key($expiry);
			}
		}
		return $rs;
	}

	public function delSessionRef($ref) {
		if ($this->debug) { $this->add2log('sessions', __METHOD__, $ref); }
		if ($this->dalmp_sessions_cache) {
			$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
			$sessions_key = 'DALMP_SESSIONS_REF'.$key;
			$refs = $this->getCache('sessions_ref', $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
			if ($refs) {
				foreach ($refs as $key => $expiry) {
					if (key($expiry) == $ref) {
						unset($refs[$key]);
					}
				}
			}
			$rs = $this->setCache('sessions_ref', $refs, 0, $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
		}
		return $this->PExecute('DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE ref=?', $ref);
	}

	protected function _sessionsRef($sid, $ref, $key, $destroy=null) {
		$sessions_key = 'DALMP_SESSIONS_REF'.$key;
		if($this->debug_sessions) {
			$this->add2log('sessions',__METHOD__,"sid: $sid, ref: $ref, key: $key, destroy: $destroy");
		}
		$refs = $this->getCache('sessions_ref', $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
		if(!$refs) {
			$refs = array();
		}
		/**
		 * expiry ref
		 */
		foreach ($refs as $key => $expiry) {
			if (current($expiry) < time()) {
				unset($refs[$key]);
			}
		}
		if (isset($destroy)) {
			unset($refs[$sid]);
		} else {
			$refs[$sid] = array($ref => time() + ini_get('session.gc_maxlifetime'));
		}
		$rs = $this->setCache('sessions_ref', $refs, 0, $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
		if(!$rs) {
			if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'ERROR', "error saving Cache - ref: $refs session key: $sessions_key cn: $this->dalmp_sessions_cname cache type: $this->dalmp_sessions_cache_type"); }
			return false;
		} else {
			if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'refs saved'); }
			return true;
		}
	}

	public function Sopen() {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__);
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$rs = $this->isConnected($this->dalmp_sessions_cname) ? true : false;
		if (!$rs) {
			if ($this->debug_sessions) {
				$this->add2log('sessions', __METHOD__, 'ERROR', $this->dalmp_sessions_cname, 'connection not available');
			}
			trigger_error('ERROR -> '. __METHOD__ .' :'. $this->dalmp_sessions_cname. 'connection not available', E_USER_NOTICE);
		}
		$this->debug = $this->debug2 ? true : false;
		return $rs;
	}

	public function Sclose() {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__);
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$this->debug = $this->debug2 ? true : false;
		return true;
	}

	public function Sread($sid) {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__, $sid);
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}

		if($this->dalmp_sessions_cache) {
			$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
			if ($cache = $this->getCache($sid, $key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type)) {
				if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'session cached', $cache); }
				$this->debug = $this->debug2 ? true : false;
				return $cache;
			} else {
				$expiry = time();
				$cache = ($rs = $this->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, $expiry, $this->dalmp_sessions_cname)) ? $rs : '';
				if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, "$this->dalmp_sessions_cache_type not running or responding, reading cache from DB, sid: $sid"); }
				$this->debug = $this->debug2 ? true : false;
				return $cache;
			}
		} else {
			$expiry = time();
			$cache = ($rs = $this->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, $expiry, $this->dalmp_sessions_cname)) ? $rs : '';
			if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, "returned from db: $cache"); }
			$this->debug = $this->debug2 ? true : false;
			return $cache;
		}
	}

	public function Swrite($sid, $data) {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__, "sid: $sid, data: $data");
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$field = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : null;
		$ref = (isset($GLOBALS[$field]) && !empty($GLOBALS[$field])) ? $GLOBALS[$field] : null;

		$timeout = ini_get('session.gc_maxlifetime');
		if($this->dalmp_sessions_cache) {
			$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
			$rs = $this->setCache($sid, $data, $timeout, $key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
			if (!$rs) {
				$write2db = true;
				if ($this->debug_sessions) {
					$this->add2log('sessions', __METHOD__, 'ERROR', "setCache, $this->dalmp_sessions_cache_type not running or responding, sid: $sid, data: $data, timeout: $timeout, cn: $this->dalmp_sessions_cname");
				}
				trigger_error("Cache: $this->dalmp_sessions_cache_type, not running or responding", E_USER_NOTICE);
			} else {
				if ($this->debug_sessions) {
					$this->add2log('sessions', __METHOD__, "setCache -> $this->dalmp_sessions_cache_type, sid: $sid, data: $data, timeout: $timeout, cn: $this->dalmp_sessions_cname");
				}
				/**
				 * store REF on cache
				 */
			  if (isset($ref)) {
					if (!$this->_sessionsRef($sid, $ref, $key)) {
						$write2db = true;
					}
				}
		  }
		} else {
			$write2db = true;
		}

		if (isset($write2db) || defined('DALMP_SESSIONS_REDUNDANCY'))  {
			$sql = "REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES(?,?,?,?)";
			$expiry = time() + ini_get('session.gc_maxlifetime');
			$rs = $this->PExecute($sql, $sid, $expiry, $data, $ref, $this->dalmp_sessions_cname);
			if ($this->debug_sessions) {
				$this->add2log('sessions', __METHOD__, "writing to db: sql: $sql, data: $data, expiry: $expiry, sid: $sid, ref: $ref, cn: $this->dalmp_sessions_cname");
			}
		}
		$this->debug = $this->debug2 ? true : false;
		return true;
	}

	public function Sdestroy($sid) {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__, $sid);
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE sid=?';
		$rs = $this->PExecute($sql, $sid, $this->dalmp_sessions_cname);

		if($this->dalmp_sessions_cache) {
			$key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
			$this->CacheFlush($sid, $key, $this->dalmp_sessions_cname);
			/**
			 * destroy REF on cache
			 */
			$field = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : null;
			$ref = isset($GLOBALS[$field]) ? $GLOBALS[$field] : null;
			if (isset($ref)) {
				$rs = $this->_sessionsRef($sid, $ref, $key, true);
			}
		}
		$this->debug = $this->debug2 ? true : false;
		return $rs;
	}

	public function Sgc() {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__);
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
		if ($this->PExecute($sql, $this->dalmp_sessions_cname)) {
			$sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
			$rs = $this->PExecute($sql, $this->dalmp_sessions_cname);
			$this->debug = $this->debug2 ? true : false;
			return $rs;
		} else {
			if ($this->debug_sessions) {
				$this->add2log('sessions', __METHOD__, 'ERROR', 'garbage collector');
			}
			return false;
		}
	}

	public function DALMP_session_regenerate_id($check_ipv4_blocks = null) {
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__, "check ipv4 blocks: $check_ipv4_blocks");
		} elseif ($this->debug) {
			$this->debug = false;
			$this->debug2 = true;
		}
		$fingerprint = 'DALMP-|' . php_uname() . @$_SERVER['HTTP_ACCEPT_LANGUAGE'] . @$_SERVER['HTTP_USER_AGENT'] . '|';
		if ($check_ipv4_blocks) {
			$num_blocks = abs($check_ipv4_blocks);
			if ($num_blocks > 4) {
				$num_blocks = 4;
			}
			if (isset($_SERVER['REMOTE_ADDR'])) { // pendinv validationg for ipv6
				$blocks = explode('.', $_SERVER['REMOTE_ADDR']);
				for ($i = 0; $i < $num_blocks; $i++) {
					$fingerprint.= $blocks[$i] . '.';
				}
			}
		}
		if ($this->debug_sessions) {
			$this->add2log('sessions', __METHOD__, "fingerprint: $fingerprint");
		}
		$fingerprint = sha1($fingerprint);
		$old_sid = session_id();
		if ((isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] != $fingerprint)) {
			$_SESSION = array();
			session_destroy();
			if ($this->debug_sessions) {
				$this->add2log('sessions', __METHOD__, 'Session fixation', "session destroyed: sid: $old_sid, fingerprint: $fingerprint");
			}
		} else {
			session_regenerate_id(1);
		}
		$_SESSION['fingerprint'] = $fingerprint;
		if ($this->debug_sessions) {
			$sid = session_id();
			$this->add2log('sessions', __METHOD__, "old sid: $old_sid, new sid: $sid, fingerprint: $fingerprint");
		}
		$this->debug = $this->debug2 ? true : false;
		return;
	}

	public function SessionStart($start = null, $cache = null, $cn = null) {
		if (isset($start) and $start == 1) {
			$start = 1;
		} else {
			$cache = $start;
			$cn = $cache;
			$start = 0;
		}
		if (in_array($cache, $this->_cacheOrder)) {
			$this->dalmp_sessions_cache_type = $cache;
			$this->dalmp_sessions_cache = true;
		} else {
			$cache = null;
		}
		$this->dalmp_sessions_cname = in_array($cn, array_keys($this->_connection)) ? $cn : $this->cname;
		$this->dalmp_sessions_table = defined('DALMP_SESSIONS_TABLE') ? DALMP_SESSIONS_TABLE : $this->dalmp_sessions_table;
		if (isset($_SESSION)) {
			$this->add2log('sessions', __METHOD__, 'ERROR', 'A session is active. session_start() already called');
			return false;
		} else {
			session_module_name('user');
			session_set_save_handler(array(&$this,'Sopen'),
															 array(&$this,'Sclose'),
															 array(&$this,'Sread'),
															 array(&$this,'Swrite'),
															 array(&$this,'Sdestroy'),
															 array(&$this,'Sgc'));
			register_shutdown_function('session_write_close');

			ini_set('session.gc_maxlifetime', defined('DALMP_SESSIONS_MAXLIFETIME') ? DALMP_SESSIONS_MAXLIFETIME : get_cfg_var('session.gc_maxlifetime'));
			ini_set('session.name','DALMP_SESSID');
			ini_set('session.use_cookies', 1);
      ini_set('session.use_only_cookies', 1);
			ini_set('session.use_trans_sid', 0);
		 @ini_set('session.hash_function', 1); // sha1
		 @ini_set('session.hash_bits_per_character', 5);

			if (get_cfg_var('session.auto_start') || $start) {
				session_start();
			}
			if ($this->debug_sessions) {
				$this->add2log('sessions', __METHOD__, "start: $start, cache: $cache, cn: $this->dalmp_sessions_cname, sessions table: $this->dalmp_sessions_table");
			}
		}
	}

	public function sqlite_table_exists($db, $table) {
		if ($this->debug) { $this->add2log(__METHOD__, "db: $db, table: $table"); }
		$rs = sqlite_query($db, "SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table'");
		$count = intval(sqlite_fetch_single($rs));
		return $count > 0;
	}

	public function queue($sql = null) {
		if ($this->debug) { $this->add2log(__METHOD__, $sql); }
		if ($sql) {
			$queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : $this->dalmp_queue_db;
			$sdb = sqlite_open($queue_db);
			if (!$this->sqlite_table_exists($sdb, 'sql')) {
				sqlite_query($sdb, 'CREATE TABLE sql (
														id INTEGER PRIMARY KEY,
														sql TEXT,
														cdate DATE)');
			}
			$sql64 = base64_encode(preg_replace('/\s+/', ' ', $sql));
			$cdate = @date('Y-m-d H:i:s');
			$sql = "INSERT INTO sql VALUES (NULL, '$sql64', '$cdate')";
			$rs = sqlite_query($sdb, $sql);
			if (!$rs) {
				trigger_error("queue: could not save $sql on $queue_db", E_USER_NOTICE);
			}
		}
	}

	public function readQueue($queue = null) {
		if ($this->debug) { $this->add2log(__METHOD__, $queue); }
		$queue = isset($queue) ? $queue : $queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : $this->dalmp_queue_db;
		$db = new SQLiteDatabase($queue);
		$rs = $db->Query("SELECT * FROM sql");
		$out = array();
		while ($rs->valid()) {
			$row = $rs->current();
			$out[$row['id']]['sql'] = base64_decode($row['sql']);
			$out[$row['id']]['cdate'] = $row['cdate'];
			$rs->next();
		}
		return $out;
	}

	public function http_client($url, $expectedValue = null, $SearchInPage = true, $queue = 'default') {
		if ($this->debug) { $this->add2log(__METHOD__, $url); }
		$ch = curl_init();
		$timeout = defined('DALMP_HTTP_CLIENT_CONNECT_TIMEOUT') ? DALMP_HTTP_CLIENT_CONNECT_TIMEOUT : $this->http_client_connect_timeout;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, '(DALMP - dalmp.com)');
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$page = curl_exec($ch);
		if (isset($expectedValue)) {
			if ($SearchInPage) {
				$qurl = strstr($page, $expectedValue) === FALSE ? true : false;
			} else {
				$qurl = ($page == $expectedValue) ? false : true;
			}
		}
		if (curl_errno($ch) || $qurl) {
			$queue_db = defined('DALMP_QUEUE_URL_DB') ? DALMP_QUEUE_URL_DB : $this->dalmp_queue_url_db;
			$sdb = sqlite_open($queue_db);
			if (!$this->sqlite_table_exists($sdb, 'url')) {
				sqlite_query($sdb, 'CREATE TABLE url (
													 id INTEGER PRIMARY KEY,
													 queue VARCHAR (50) NOT NULL,
													 url VARCHAR (255) NOT NULL,
													 expectedValue VARCHAR (255) NOT NULL,
													 cdate DATE)');
			}
			$cdate = @date('Y-m-d H:i:s');
			$sql = "INSERT INTO url VALUES (NULL, '$queue', '$url', '$expectedValue', '$cdate')";
			$rs = sqlite_query($sdb, $sql);
			if (!$rs) {
				trigger_error("queue: could not save $url on $queue_db", E_USER_NOTICE);
			}
		} else {
			curl_close($ch);
			return true;
		}
	}

	public function readQueueURL($print=false, $queue = null) {
		if ($this->debug) { $this->add2log(__METHOD__, $queue); }
		$queue = isset($queue) ? $queue : defined('DALMP_QUEUE_URL_DB') ? DALMP_QUEUE_URL_DB : $this->dalmp_queue_url_db;
		$db = new SQLiteDatabase($queue);
		$rs = $db->Query("SELECT * FROM url");
		if($print) {
			while ($rs->valid()) {
				$row = $rs->current();
				$id = $row['id'];
				$queue = $row['queue'];
				$url = $row['url'];
				$eValue = $row['expectedValue'];
				$cdate = $row['cdate'];
				echo "$id|$queue|$url|$eValue|$cdate" . $this->isCli(1);
				$rs->next();
			}
		} else {
			return $rs;
		}
	}

	public function isCli($rlb = null) {
		if ($rlb) { // return line break
			return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) ? PHP_EOL : '<br />';
		} else {
			return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) ? true : false;
		}
	}

	public function getServerVersion($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		$version = $this->getConnection($cn)->server_version;
		$major = (int)($version / 10000);
		$minor = (int)($version % 10000 / 100);
		$revision = (int)($version % 100);
		return $major . '.' . $minor . '.' . $revision;
	}

	public function getClientVersion($cn = null) {
		$cn = isset($cn) ? $cn : $this->cname;
		$version = $this->getConnection($cn)->client_version;
		$major = (int)($version / 10000);
		$minor = (int)($version % 10000 / 100);
		$revision = (int)($version % 100);
		return $major . '.' . $minor . '.' . $revision;
	}

	public function UUID() {
		// http://us2.php.net/manual/en/function.com-create-guid.php#52354
		// Version 4 (random)
		if (function_exists('uuid_create')) {
			$uuid = uuid_create();
			if ($this->debug) { $this->add2log(__METHOD__, $uuid); }
			return $uuid;
		} else {
			mt_srand((double)microtime() * 10000);
			$charid = sha1(uniqid(mt_rand() , true));
			$hyphen = chr(45); // "-"
			$uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
			if ($this->debug) { $this->add2log(__METHOD__, $uuid, 'pecl uuid not installed'); }
			return $uuid;
		}
	}

	public function __call($name, $arguments) {
		die("'$name' method does not exist, args: " . implode('|', $arguments) . $this->isCli(1));
	}

	public function __toString() {
		if (count(array_keys($this->_connection)) > 0) {
			$status = null;
			foreach (array_keys($this->_connection) as $cn) {
				if($this->isConnected($cn)) {
					$status .= 'DALMP :: ';
					$status .= "connected to: $cn, ";
					$status .= 'Character set: '.$this->getConnection($cn)->character_set_name();
					$status .= ', '.$this->getConnection($cn)->host_info;
					$status .= ', Server version: '.$this->getServerVersion($cn);
					$status .= ', Client version: '.$this->getClientVersion($cn);
					$status .= ', System status: '.$this->getConnection($cn)->stat();
				}
			}
		} else {
			$status = 'no connections';
		}
		if ($this->debug) {$this->add2log(__METHOD__, $status); }
		return $status;
	}

}
?>