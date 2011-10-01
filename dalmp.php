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
 * # define('DALMP_SITE_KEY','dalmp.com');
 * # define('DALMP_SESSIONS_SQLITE_DB','/home/sites/sessions.db');
 * # define('DALMP_SESSIONS_REF', 'UID');
 * # define('DALMP_SESSIONS_KEY', 'mykey');
 * # define('DALMP_SESSIONS_REDUNDANCY', true);
 * # define('DALMP_HTTP_CLIENT_CONNECT_TIMEOUT', 1);
 * # define('DALMP_QUEUE_DB', '/tmp/queue.db');
 * # define('DALMP_DEBUG_FILE', '/tmp/dalmp/debug.log');
 * # define('DALMP_CACHE_DIR', '/tmp/dalmp/cache/');
 *
 * initialize the class:
 *
 * $db = DALMP::getInstance();
 * $db->database(DSN, $ssl);  #$ssl = array('key' => null, 'cert' => null, 'ca' => 'mysql-ssl.ca-cert.pem', 'capath' => null, 'cipher' => null);
 * # if you want to use APC
 * # $db->Cache('apc');
 * # if you want to use memcache
 * # $db->Cache('memcache',MEMCACHE_HOSTS);
 * # if you want to use redis
 * # $db->Cache('redis',REDIS_HOST, REDIS_PORT, 3);
 *
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
 * -----------------------------------------------------------------------------------------------------------------
 * @link http://code.dalmp.com
 * @copyright Nicolas de Bari Embriz <nbari@dalmp.com>
 * @version 1.321
 * -----------------------------------------------------------------------------------------------------------------
 */
if (!defined('DALMP_DIR')) define('DALMP_DIR', dirname(__FILE__));

class DALMP {
  /**
   * Contains the database instance.
   * @access private
   * @var instance
   */
  private static $db_instance;

  /**
   * Contains the database parameters DSN.
   * @access private
   * @var array
   */

  private $dsn = array();
  /**
   * Holds the fetchMode.
   * @access private
   * @var mixed
   */
  private $fetchMode = MYSQLI_BOTH;

  /**
   * Holds the num of rows returned.
   * @access private
   * @var int
   */
  private $numOfRows = null;

  /**
   * Holds the num of fields returned.
   * @access private
   * @var int
   */
  private $numOfFields = null;

  /**
   * Holds the connection name
   * @access private
   * @var mixed
   */
  private $cname = null;

  /**
   * connection timeout in seconds
   * @access private
   * @var int
   */
  private $connect_timeout = 30;

  /**
   * Contains database connection information.
   * @access private
   * @var array
   */
  protected $_connection = array();

  /**
   * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a result object.
   * For other successful queries mysqli_query() will return TRUE.
   * @access protected
   * @var mixed
   */
  protected $_rs = null;

  /**
   * returns a statement object or FALSE if an error occurred.
   * @access protected
   * @var mixed
   */
  protected $_stmt = null;

  /**
   * Contains the allowed paramteres for the prepared statments
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
   * memcache connection
   * @access protected
   * @var mixed
   */
  protected $_memcache = null;

  /**
   * redis connection
   * @access protected
   * @var mixed
   */
  protected $_redis = null;

  /**
   * sqlite3 object
   * @access protected
   * @var object
   */
  protected $_sdb = null;

  /**
   * memcache hosts
   * @access private
   * @var array
   */
  private $memCacheHosts = array();

  /**
   * Use memcache compression
   * @access private
   * @var boolean
   */
  private $memCacheCompress = 0;

  /**
   * cache timeout in seconds, default to 1h
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
   * Contains the prepared statments parameters
   * @access private
   * @var array
   */
  private $stmtParams = array();

  /**
   * connection name to use for storing sessions
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_cname = null;

  /**
   * table to use for sessions
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
   * group cache on group:session
   * #access private
   * @var string
   */
  private $dalmp_sessions_group_cache = false;

  /**
   * sqlite database for sessions
   * @access private
   * @var mixed
   */
  private $dalmp_sessions_sqlite_db = 'dalmp_sessions.db';

  /**
   * sqlite database to use for queueing
   * @access private
   * @var mixed
   */
  private $dalmp_queue_db = 'queue.db';

  /**
   * sqlite database to use for queueing http requests
   * @access private
   * @var mixed
   */
  private $dalmp_queue_url_db = 'queue_url.db';

  /**
   * http_client connect_timeout  to use for queueing http requests
   * @access private
   * @var mixed
   */
  private $http_client_connect_timeout = 3;

  /**
   * If enabled, logs all queries and executions.
   * @access private
   * @var boolean
   */
  private $debug = false;

   /**
    * If enabled, logs all queries and executions for the sessions.
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
   * @var int
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
      if(!file_exists($debugFile)) {
        @mkdir(dirname($debugFile),0700,true);
      }
      if ($this->debug2file > 1) { $debugFile .= '-'.microtime(true); }
      $fh = fopen($debugFile, 'a+');
      $start = str_repeat('-', 80) . PHP_EOL;
      fwrite($fh, 'START ' . @date('c') . PHP_EOL);
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
      fwrite($fh, 'END ' . @date('c') . PHP_EOL);
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
      $this->debug2file = $log2file;
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

  public function database($dsn = null, $ssl = null) {
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
      $this->_connect($this->dsn['cname'], $ssl);
    } else {
      die("DSN missing: define('DSN', DB_CHARSET.'://'.DB_USERNAME.':'.DB_PASSWORD.'@'.DB_HOST.':'.DB_PORT.'/'.DB_DATABASE.'?'.DB_CNAME);");
    }
  }

  protected function _connect($cn, $ssl = false) {
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
    if (is_array($ssl)) {
      if ($this->debug) { $this->add2log('DSN - SSL', $ssl); }
      mysqli_ssl_set($this->_connection[$cn], $ssl['key'], $ssl['cert'], $ssl['ca'], $ssl['capath'], $ssl['cipher']);
    }
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
    return empty($this->_connection[$cn]) ? false : (bool)($this->_connection[$cn] instanceof mysqli);
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

  public function Prepare() {
    switch(func_num_args()) {
      case 1:
        $param = func_get_arg(0);
        $clean = true;
      break;
      case 2:
        $key = func_get_arg(0);
        $param = func_get_arg(1);
        if (in_array($key, $this->_allowedParams, true)) {
          $this->stmtParams[] = array($key => $param);
        } else {
          $clean = true;
        }
      break;
      default:
        return $this->stmtParams;
      break;
    }
    if (isset($clean)) {
      if (is_numeric($param)) {
        $param = !strcmp(intval($param), $param) ? (int)$param : (!strcmp(floatval($param), $param) ? (float)$param : $param);
      }
      $key = is_int($param) ? 'i' : (is_float($param) ? 'd' : (is_string($param) ? 's' : 'b'));
      return $this->stmtParams[] = array($key => $param);
    }
  }

  /**
   * Prepared Statements
   * arguments: $sql, $params, $cn
   * example: PGetAll('SELECT * FROM users WHERE name=? AND id=?', 'name', 1, 'db1')
   * user also can define  the corresponding type of the bind variables (i, d, s, b): http://pt.php.net/manual/en/mysqli-stmt.bind-param.php
   * example: PGetAll('SELECT * FROM table WHERE name=? AND id=?', array('s'=>'name', 'i'=>1), 'db1'); or use the Prepare() method
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
      if (is_array(current($args))) {
        foreach ($args as $arg) {
          foreach ($arg as $key => $param) {
            $types .= $key;
            $params[] = &$args[$i][$key];
            $i++;
          }
        }
      } else {
        foreach ($args as $key => $param) {
          if (is_int($key)) {
            $params[] = &$args[$i];
          } else {
            $params[] = &$args[$key];
          }
          if (!in_array($key, $this->_allowedParams, true)) {
            if (is_numeric($param)) {
              $param = !strcmp(intval($param), $param) ? (int)$param : (!strcmp(floatval($param), $param) ? (float)$param : $param);
            }
            $key = is_int($param) ? 'i' : (is_float($param) ? 'd' : (is_string($param) ? 's' : 'b'));
          }
          $types.= $key;
          $i++;
        }
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
      throw new ErrorException(__METHOD__.'ERROR -> '.$this->getConnection($cn)->error." - sql: $sql on $cn with params: ".json_encode($params));
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
  public function ErrorNum ($cn = null) {
    $cn = isset($cn) ? $cn : $this->cname;
    return $this->getConnection($cn)->errno;
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
      throw new ErrorException(__METHOD__.'ERROR -> '.$this->getConnection($cn)->error." - sql: $sql on $cn");
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
      $row = $this->_rs->fetch_row();
      $this->Close();
      return reset($row);
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

  public function renumber($table, $row='id', $cn=null) {
    $cn = isset($cn) ? $cn : $this->cname;
    if (isset($table)) {
      return $this->Execute('SET @var_dalmp=0', $cn) ? ($this->Execute("UPDATE $table SET $row = (@var_dalmp := @var_dalmp +1)",$cn) ? $this->Execute("ALTER TABLE $table AUTO_INCREMENT = 1", $cn) : false) : false;
    } else {
      return false;
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
    $this->memCacheHosts = explode(';', $hosts);
    $this->memCacheCompress = $compress ? MEMCACHE_COMPRESSED : 0;

    foreach ($this->memCacheHosts as $server) {
      $out = explode(':', $server);
      $host = trim($out[0]);
      if(strstr($host,'/')) {
        $port = 0;
        if ($this->debug) { $this->add2log('memCache', __METHOD__, "adding server via socket: $host"); }
        $host = "unix://$host";
      } else {
        $port = isset($out[1]) ? trim($out[1]) : 11211;
        if ($this->debug) { $this->add2log('memCache', __METHOD__, "adding server via host: $host port: $port"); }
      }
      if(!@$memcache->addServer($host, $port, true)) {
        if ($this->debug) { $this->add2log('memCache', __METHOD__, 'ERROR', "Can not add server $server"); }
      }
    }

    if (!@$memcache->getVersion()) {
      if ($this->debug) { $this->add2log('memCache', __METHOD__, 'ERROR', 'Can not connect to any memcache server'); }
      return false;
    } else {
      $this->_memcache = $memcache;
      return true;
    }
  }

  public function isMemcacheConnected() {
    return (bool)($this->_memcache instanceof MemCache);
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
      trigger_error('ERROR ->'. __METHOD__.': redis extension not loaded! - http://github.com/owlient/phpredis', E_USER_NOTICE);
      return false;
    }
    $timeout = isset($timeout) ? $timeout : 1;
    try {
      if($this->debug) { $this->add2log('redis', __METHOD__,"connecting to $host port: $port timeout: $timeout"); }
      $redis = new Redis();
      if($redis->connect($host, $port, $timeout)) {
        $this->_redis = $redis;
        if($this->debug) { $this->add2log('redis', __METHOD__,"connected to $host port: $port timeout: $timeout"); }
        return true;
      } else {
        if($this->debug) { $this->add2log('redis', __METHOD__,'ERROR', "can not connect to $host port: $port timeout: $timeout"); }
        return false;
      }
    } catch (RedisException $e) {
      if($this->debug) { $this->add2log('redis', __METHOD__,'ERROR',$e->getMessage()); }
    }
  }

  public function isRedisCacheConnected() {
    return (bool)($this->_redis instanceof Redis);
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

  public function redisX() {
    return  $this->isRedisCacheConnected() ? $this->_redis : false;
  }

  public function memCacheX() {
    return $this->isMemcacheConnected() ? $this->_memcache : false;
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

  public function set($cache=null, $key=null, $value=null, $timeout=0){
    $caches = $this->_cacheOrder;
    array_pop($caches);
    $ct = in_array(strtolower($cache), $caches) ? $cache : false;
    if(!$ct) {
      $value = $key;
      $key = $cache;
      $ct = reset($caches);
    }
    if ($this->debug) { $this->add2log(__METHOD__, $ct, "key: $key value: $value timeout: $timeout"); }
    switch($ct){
      case 'apc':
        return $this->apcCache() ? apc_store($key, $value, $timeout) : false;
        break;
      case 'memcache':
        return $this->isMemcacheConnected() ? $this->_memcache->set($key, $value, $this->memCacheCompress, $timeout) : false;
        break;
      case 'redis':
        if ($timeout == 0 || $timeout == -1) {
          return $this->isRedisCacheConnected() ? $this->_redis->set($key, $value) : false;
        } else {
          return $this->isRedisCacheConnected() ? $this->_redis->setex($key, $timeout, $value) : false;
        }
        break;
    }
  }

  public function get($cache=null, $key=null){
    $caches = $this->_cacheOrder;
    array_pop($caches);
    $ct = in_array(strtolower($cache), $caches) ? $cache : false;
    if(!$ct) {
      $key = $cache;
      $ct = reset($caches);
    }
    if ($this->debug) { $this->add2log(__METHOD__, $ct, "key: $key"); }
    switch($ct){
      case 'apc':
        return $this->apcCache() ? apc_fetch($key) : false;
        break;
      case 'memcache':
        return $this->isMemcacheConnected() ? $this->_memcache->get($key) : false;
        break;
      case 'redis':
        return $this->isRedisCacheConnected() ? $this->_redis->get($key) : false;
        break;
    }
  }

  public function delete($cache=null, $key=null){
    $caches = $this->_cacheOrder;
    array_pop($caches);
    $ct = in_array(strtolower($cache), $caches) ? $cache : false;
    if(!$ct) {
      $key = $cache;
      $ct = reset($caches);
    }
    if ($this->debug) { $this->add2log(__METHOD__, $ct, "key: $key"); }
    switch($ct){
      case 'apc':
        return apc_delete($key);
        break;
      case 'memcache':
        return $this->isMemcacheConnected() ? $this->_memcache->delete($key) : false;
        break;
      case 'redis':
        return $this->isRedisCacheConnected() ? $this->_redis->delete($key) : false;
        break;
    }
  }

  /**
   * setCache arguments: $sql, $object, $timeout, $key, $group, $cn (connection name), $dc (use dir cache), $ct (cache type)
   */
  public function setCache($sql, $object, $timeout, $key, $group, $cn, $dc=true, $ct=null) {
    $cn = isset($cn) ? $cn : $this->cname;
    $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
    $hkey = sha1($skey . $sql . $key . $cn);
    $ghkey = (isset($group) AND (strncmp($group,'group:',6) == 0)) ? sha1($skey."cache_group$group$cn") : null;
    if($ghkey) {
      if ($this->debug) { $this->add2log('CacheGroup', __METHOD__, "$group key: $ghkey"); }
      $gCache = $this->getCache('cache_group', $group, $cn, false);
      if(!$gCache) {
        $gCache = array();
      } else {
        foreach ($gCache as $key => $expiry) {
          if ($expiry < time()) {
            unset($gCache[$key]);
          }
        }
      }
      $gCache[$hkey] = time() + $timeout;
    }
    $ct = isset($ct) ? $ct : $this->_cacheType;
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $ct","hkey: $hkey object: $sql timeout: $timeout key: $key group: $group cn: $cn dc: $dc object: ",$object); }
    $rs = false;
    switch($ct) {
      case 'apc':
        $rs = $this->apcCache() ? apc_store($hkey, $object, $timeout) : false;
        if ($this->debug) {
          $this->add2log('Cache',__METHOD__." - $ct","STORE key: $hkey timeout: $timeout object:",$object);
          if(!$rs) {
            $this->add2log('Cache',__METHOD__." - $ct",'APC not responding');
          }
        }
        if($ghkey && $rs) {
          $rs2 = apc_store($ghkey, $gCache, 0);
          if ($this->debug) {
            $this->add2log('CacheGroup',__METHOD__." - $ct","$group STORE gkey: $ghkey object:",$gCache);
            if(!$rs2) {
              $this->add2log('CacheGroup',__METHOD__." - $ct",'APC not responding');
            }
          }
        }
        break;
      case 'memcache':
        $rs = $this->isMemcacheConnected() ? $this->_memcache->set($hkey, $object, $this->memCacheCompress, $timeout) : false;
        if ($this->debug) {
          $this->add2log('Cache',__METHOD__." - $ct","SET key: $hkey compress: $this->memCacheCompress timeout: $timeout, object: ",$object);
          if(!$rs) {
            $this->add2log('Cache',__METHOD__." - $ct",'memcache daemon not running or responding.');
          }
        }
        if($ghkey && $rs) {
          $rs2 = $this->_memcache->set($ghkey, $gCache, $this->memCacheCompress, 0);
          if ($this->debug) {
            $this->add2log('CacheGroup',__METHOD__." - $ct","$group SET gkey: $ghkey object:",$gCache,"returned: $rs2");
            if(!$rs2) {
              $this->add2log('CacheGroup',__METHOD__." - $ct",'memcache daemon not running or responding.');
            }
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
          $this->add2log('Cache',__METHOD__." - $ct","SET key: $hkey timeout: $timeout object:", $object);
          if(!$rs) {
            $this->add2log('Cache',__METHOD__." - $ct",'redis daemon not running or responding.');
          }
        }
        if($ghkey && $rs) {
          $rs2 = $this->_redis->set($ghkey, serialize($gCache));
          if($this->debug) {
            $this->add2log('CacheGroup',__METHOD__." - $ct","$group SET gkey: $ghkey object:", $gCache);
            if(!$rs2) {
              $this->add2log('CacheGroup',__METHOD__." - $ct",'redis daemon not running or responding.');
            }
          }
        }
        break;
    }
    if(!$rs && $dc) {
      if ($this->debug && $this->_cacheType != 'dir') {
        $this->add2log('Cache', __METHOD__." - $ct",'ERROR','not responding, using dir cache');
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
      if ($this->debug) { $this->add2log('Cache', __METHOD__." - $ct","cache for hkey $hkey returned: $rs"); }
      return $rs;
    }
  }

  /**
   * getCache arguments: $sql, $key, $cn (connection name), $dc (use dir cache), $ct (cache type)
   */
  public function getCache($sql, $key, $cn, $dc=true, $ct=null) {
    $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
    $hkey = sha1($skey . $sql . $key. $cn);
    $ct = isset($ct) ? $ct : $this->_cacheType;
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $ct", "hkey: $hkey, sql: $sql key: $key cn: $cn dc: $dc ct: $ct"); }
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
      if ($this->debug) { $this->add2log('Cache', __METHOD__." - $ct", "cache retrieved for hkey: $hkey"); }
      return $rs;
    }
  }

  public function CacheFlush($sql=null, $key=null, $cn=null, $cache=null) {
    if ($sql) {
      $cn = isset($cn) ? $cn : $this->cname;
      $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
      $hkey = sha1($skey . $sql . $key . $cn);
      if ($this->debug) { $this->add2log('Cache', __METHOD__, "flush hkey: $hkey, sql: $sql, key: $key, cn: $cn on: ". (isset($cache) ? $cache : implode(', ', $this->_cacheOrder))); }

      $this->_cacheOrder = in_array($cache, $this->_cacheOrder) ? array($cache) : $this->_cacheOrder;

      foreach($this->_cacheOrder as $value) {
        if ((strncmp($sql,'group:',6) == 0) AND $value != 'dir') {
          $group = $this->getCache('cache_group', $sql, $cn, false, $value);
          $group = is_array($group) ? $group : array();
          $group[sha1($skey."cache_group$sql$cn")] = $sql;
          if ($this->debug) { $this->add2log('CacheGroup', __METHOD__, "flushing $sql on $value with keys:",$group); }
        }
        switch($value) {
          case 'apc':
            if (isset($group)) {
              $rs = array();
              foreach($group as $key => $timeout) {
                if(!apc_delete($key)) {
                  $rs[] = $key;
                }
              }
              if ($this->debug) { count($rs) ? $this->add2log('CacheGroup',$value,'ERROR',"could not flush: $sql keys:", $rs) : $this->add2log('CacheGroup',$value,"flushed $sql"); }
            } else {
              $rs = apc_delete($hkey);
              if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, "flushed hhey: $hkey") : $this->add2log('Cache', $value, 'ERROR', "could not flush hkey: $hkey"); }
            }
            break;
          case 'memcache':
            if (isset($group) AND $this->isMemcacheConnected()) {
              $rs = array();
              foreach($group as $key => $timeout) {
                if(!$this->_memcache->delete($key)) {
                  $rs[] = $key;
                }
              }
              if ($this->debug) { count($rs) ? $this->add2log('CacheGroup',$value,'ERROR',"could not flush: $sql keys:", $rs) : $this->add2log('CacheGroup',$value,"flushed $sql"); }
            } else {
              $rs = $this->isMemcacheConnected() ? $this->_memcache->delete($hkey) : false;
              if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, "flushed hhey: $hkey") : $this->add2log('Cache', $value, 'ERROR', "could not flush hkey: $hkey"); }
            }
            break;
          case 'redis':
            if (isset($group) AND $this->isRedisCacheConnected()) {
              $rs = array();
              foreach($group as $key => $timeout) {
                if(!$this->_redis->delete($key)) {
                  $rs[] = $key;
                }
              }
              if ($this->debug) { count($rs) ? $this->add2log('CacheGroup',$value,'ERROR',"could not flush: $sql keys:", $rs) : $this->add2log('CacheGroup',$value,"flushed $sql"); }
            } else {
              $rs = $this->isRedisCacheConnected() ? $this->_redis->delete($hkey) : false;
              if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, "flushed hhey: $hkey") : $this->add2log('Cache', $value, 'ERROR', "could not flush hkey: $hkey"); }
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
            if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, 'flushed all') : $this->add2log('Cache', $value, 'ERROR','could not flush all'); }
            break;
          case 'memcache':
            $rs = $this->isMemcacheConnected() ? $this->_memcache->flush() : false;
            if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, 'flushed all') : $this->add2log('Cache', $value, 'ERROR','could not flush all'); }
            break;
          case 'redis':
            $rs = $this->isRedisCacheConnected() ? $this->_redis->flushDB() : false;
            if ($this->debug) { ($rs) ? $this->add2log('Cache', $value, 'flushed all') : $this->add2log('Cache', $value, 'ERROR','could not flush all'); }
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
   * args: $method, $cachetype, $timeout, $sql, $key, $group, $cn
   */
  protected function _Cache() {
    $args = func_get_args();
    $fetch = array_shift($args);
    $cachetype = reset($args);
    $cachetype = in_array(strtolower($cachetype), $this->_cacheOrder) ? array_shift($args) : reset($this->_cacheOrder);
    $this->_cacheType = $cachetype;
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", 'args: ', $args); }
    $timeout = reset($args);
    $timeout = (!is_numeric($timeout)) ? $this->timeout: array_shift($args);
    $sql = array_shift($args);
    $cn = end($args);
    if (in_array($cn, array_keys($this->_connection))) {
      $cn = array_pop($args);
      $this->cname = $cn;
    } else {
      $cn = $this->cname;
    }
    $key = isset($args[0]) ? $args[0] : $fetch;
    if (strncmp($key,'group:',6) == 0) {
      $group = $key;
      $key = $fetch;
    } else {
      $group = (isset($args[1]) AND (strncmp($args[1],'group:',6) == 0)) ? $args[1] : null;
    }
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", "method: $fetch timeout: $timeout sql: $sql key: $key group: $group cn: $cn"); }
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
      if (!$this->setCache($sql, $cache, $timeout, $key, $group, $cn)) {
        if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", 'ERROR', "setCache: Error saving data to cache sql: $sql cache: ", $cache," timeout: $timeout key: $key cn: $cn group: $group"); }
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
   * args: $method, $cachetype, $timeout, $sql, $key, $group, $cn
   */
  protected function _CacheP() {
    $args = func_get_args();
    $fetch = array_shift($args);
    $cachetype = reset($args);
    $cachetype = in_array(strtolower($cachetype),$this->_cacheOrder) ? array_shift($args) : reset($this->_cacheOrder);
    $this->_cacheType = $cachetype;
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", 'PreparedStatements args: ', $args); }
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
    $targs = count($args);
    $args = is_array(current($args)) ? current($args) : $args;
    if($targs > $eparams) {
      if(($targs - $eparams) == 1) {
        $key = array_pop($args);
        $params = $args;
        if (strncmp($key,'group:',6) == 0) {
          $group = $key;
          $key = $fetch.implode('|',array_merge(array_keys($args),$args));
        }
      } else {
        $group = array_pop($args);
        $group = (strncmp($group,'group:',6) == 0) ? $group : null;
        $key = array_pop($args);
        $params = $args;
      }
    } else {
      $key = $fetch.implode('|',array_merge(array_keys($args),$args));
      $params = $args;
      $group = null;
    }
    array_unshift($args, $sql);
    array_push($args, $cn);
    if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", "PreparedStatements method: $fetch timeout: $timeout, sql: $sql params: ".implode('|',$params)." key: $key cn: $cn group: $group"); }
    if ($cache = $this->getCache($sql, $key, $cn)) {
      return $cache;
    } else {
      if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", 'PreparedStatements no cache returned, executing query PExecute with args: ',$args); }
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
      if (!$this->setCache($sql, $cache, $timeout, $key, $group, $cn)) {
        if ($this->debug) { $this->add2log('Cache', __METHOD__." - $cachetype", 'ERROR', "setCache: Error saving data to cache sql: $sql, params: ".implode('|',$params)." cache: ",$cache," timeout: $timeout key: $key cn: $cn group: $group"); }
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

  public function getSessionsRefs($expiry=null) {
    $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
    $sessions_key = 'DALMP_SESSIONS_REF'.$key;
    if ($this->debug) { $this->add2log('sessions', __METHOD__, $sessions_key); }
    $cached_refs = $this->getCache('sessions_ref', $sessions_key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
    $dba = array();
    if($this->dalmp_sessions_cname == 'sqlite') {
      $db_refs = isset($expiry) ? $this->_sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > strftime('%s','now')") : $this->_sdb->query("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
      while($value = $db_refs->fetchArray(SQLITE3_ASSOC)) {
        $dba[$value['sid']] = array($value['ref'] => $value['expiry']);
      }
    } else {
      $db_refs = isset($expiry) ? $this->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table WHERE expiry > UNIX_TIMESTAMP()") : $this->GetAll("SELECT sid, ref, expiry FROM $this->dalmp_sessions_table");
      if ($db_refs) {
        foreach ($db_refs as $value) {
          $dba[$value['sid']] = array($value['ref'] => $value['expiry']);
        }
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
      $rs = $this->setCache('sessions_ref', $refs, 0, $sessions_key, false, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
    }
    return ($this->dalmp_sessions_cname == 'sqlite') ? $this->_sdb->exec("DELETE FROM $this->dalmp_sessions_table WHERE ref='$ref'") : $this->PExecute('DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE ref=?', $ref);
  }

  protected function _sessionsRef($sid, $ref, $key, $destroy=null) {
    $sessions_key = 'DALMP_SESSIONS_REF'.$key;
    if($this->debug_sessions) { $this->add2log('sessions',__METHOD__,"sid: $sid ref: $ref key: $key destroy: $destroy"); }
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
    $rs = $this->setCache('sessions_ref', $refs, 0, $sessions_key, false, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
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
    if($this->dalmp_sessions_cache) {
      switch($this->dalmp_sessions_cache_type) {
        case 'memcache':
          $rs = $this->isMemcacheConnected();
          break;
        case  'redis':
          $rs = $this->isRedisCacheConnected();
          break;
      }
      if(!$rs) {
        if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'ERROR',"$this->dalmp_sessions_cache_type not running orresponding"); }
        $write2db = true;
        $this->dalmp_sessions_cache = false;
        trigger_error("Cache: $this->dalmp_sessions_cache_type, not running or responding", E_USER_NOTICE);
      } else {
        if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, "cache $this->dalmp_sessions_cache_type ok"); }
      }
    } else {
      $write2db = true;
    }

    if(isset($write2db) || defined('DALMP_SESSIONS_REDUNDANCY')) {
      if($this->dalmp_sessions_cname == 'sqlite') {
        $this->_sdb = new SQLite3($this->dalmp_sessions_sqlite_db);
        $this->_sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
        $rs = $this->_sdb->exec('CREATE TABLE IF NOT EXISTS '. $this->dalmp_sessions_table .' (sid varchar(40) NOT NULL, expiry INTEGER NOT NULL, data text, ref text, PRIMARY KEY(sid)); CREATE INDEX IF NOT EXISTS "dalmp_index" ON '. $this->dalmp_sessions_table .' ("sid" DESC, "expiry" DESC, "ref" DESC)');
      } else {
        $rs =$this->isConnected($this->dalmp_sessions_cname);
        if (!$rs) {
          if ($this->debug_sessions) {
            $this->add2log('sessions', __METHOD__, 'ERROR', $this->dalmp_sessions_cname, 'connection not available');
            trigger_error('ERROR -> '. __METHOD__ .' :'. $this->dalmp_sessions_cname. 'connection not available', E_USER_NOTICE);
          }
        }
      }
    }
    $this->debug = $this->debug2 ?: false;
    return $rs;
  }

  public function Sclose() {
    if ($this->debug_sessions) {
      $this->add2log('sessions', __METHOD__);
    } elseif ($this->debug) {
      $this->debug = false;
      $this->debug2 = true;
    }
    if($this->dalmp_sessions_cname == 'sqlite') {
      $this->_sdb->close();
    }
    $this->debug = $this->debug2 ?: false;
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
      $cache = $this->getCache($sid, $key, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
      if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'session cached', $cache); }
      $this->debug = $this->debug2 ?: false;
      return $cache;
    } else {
      $expiry = time();
      if($this->dalmp_sessions_cname == 'sqlite') {
        $rs = $this->_sdb->querySingle("SELECT data FROM $this->dalmp_sessions_table WHERE sid='$sid' AND expiry >= $expiry");
        $cache = $rs ?: false;
      } else {
        $cache = ($rs = $this->PGetOne('SELECT data FROM ' . $this->dalmp_sessions_table . ' WHERE sid=? AND expiry >=?', $sid, $expiry, $this->dalmp_sessions_cname)) ? $rs : '';
      }
      if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, "returned from db: $cache"); }
      $this->debug = $this->debug2 ?: false;
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
      $rs = $this->setCache($sid, $data, $timeout, $key, $this->dalmp_sessions_group_cache, $this->dalmp_sessions_cname, false, $this->dalmp_sessions_cache_type);
      if (!$rs) {
        $write2db = true;
        if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'ERROR', "setCache, $this->dalmp_sessions_cache_type not running or responding - sid: $sid data: $data timeout: $timeout cn: $this->dalmp_sessions_cname"); }
        trigger_error("Cache: $this->dalmp_sessions_cache_type, not running or responding", E_USER_NOTICE);
      } else {
        if ($this->debug_sessions) {
          $this->add2log('sessions', __METHOD__, "setCache -> $this->dalmp_sessions_cache_type - sid: $sid data: $data timeout: $timeout cn: $this->dalmp_sessions_cname");
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
      $expiry = time() + ini_get('session.gc_maxlifetime');
      if($this->dalmp_sessions_cname == 'sqlite') {
        $sql = "INSERT OR REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES ('$sid',$expiry,'$data','$ref')";
        $this->_sdb->exec($sql);
      } else {
        $sql = "REPLACE INTO $this->dalmp_sessions_table (sid, expiry, data, ref) VALUES(?,?,?,?)";
        $this->PExecute($sql, $sid, $expiry, $data, $ref, $this->dalmp_sessions_cname);
      }
      if ($this->debug_sessions) {
        $this->add2log('sessions', __METHOD__, "writing to db: sql: $sql data: $data expiry: $expiry sid: $sid ref: $ref cn: $this->dalmp_sessions_cname");
      }
    }
    $this->debug = $this->debug2 ?: false;
    return true;
  }

  public function Sdestroy($sid) {
    if ($this->debug_sessions) {
      $this->add2log('sessions', __METHOD__, $sid);
    } elseif ($this->debug) {
      $this->debug = false;
      $this->debug2 = true;
    }

    if($this->dalmp_sessions_cache) {
      $key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : $this->dalmp_sessions_table;
      $rs = $this->CacheFlush($sid, $key, $this->dalmp_sessions_cname, $this->dalmp_sessions_cache_type);
      if(!$rs) {
        $alsoDB = true;
        if ($this->debug_sessions) { $this->add2log('sessions',__METHOD__,'ERROR',"could not flush sid: $sid from cache: $this->dalmp_sessions_cache_type"); }
      } else {
        if ($this->debug_sessions) { $this->add2log('sessions',__METHOD__,"flushed sid: $sid from cache: $this->dalmp_sessions_cache_type"); }
      }
      /**
       * destroy REF on cache
       */
      $field = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : null;
      $ref = isset($GLOBALS[$field]) ? $GLOBALS[$field] : null;
      if (isset($ref)) {
        $rs = $this->_sessionsRef($sid, $ref, $key, true);
      }
    } else {
      $alsoDB = true;
    }

    if(isset($alsoDB) || defined('DALMP_SESSIONS_REDUNDANCY')) {
      if ($this->debug_sessions) { $this->add2log('sessions',__METHOD__,"deleting: $sid from DB"); }
      if($this->dalmp_sessions_cname == 'sqlite') {
        $sql = "DELETE FROM $this->dalmp_sessions_table WHERE sid='$sid'";
        $rs = $this->_sdb->exec($sql);
      } else {
        $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE sid=?';
        $rs = $this->PExecute($sql, $sid, $this->dalmp_sessions_cname);
      }
    }
    $this->debug = $this->debug2 ?: false;
    return $rs;
  }

  public function Sgc() {
    if ($this->debug_sessions) {
      $this->add2log('sessions', __METHOD__);
    } elseif ($this->debug) {
      $this->debug = false;
      $this->debug2 = true;
    }
    if($this->dalmp_sessions_cname == 'sqlite') {
      $sql = "DELETE FROM $this->dalmp_sessions_table WHERE expiry < ".time();
      $this->_sdb->exec($sql);
      $this->_sdb->exec('VACUUM');
      $this->debug = $this->debug2 ?: false;
      return true;
    } else {
      $sql = 'DELETE FROM ' . $this->dalmp_sessions_table . ' WHERE expiry < UNIX_TIMESTAMP()';
      if ($this->PExecute($sql, $this->dalmp_sessions_cname)) {
        $sql = 'OPTIMIZE TABLE ' . $this->dalmp_sessions_table;
        $rs = $this->PExecute($sql, $this->dalmp_sessions_cname);
        $this->debug = $this->debug2 ?: false;
        return $rs;
      } else {
        if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'ERROR', 'garbage collector'); }
        $this->debug = $this->debug2 ?: false;
        return false;
      }
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
      if (isset($_SERVER['REMOTE_ADDR'])) { // pending validation for ipv6
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
    if ( (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] != $fingerprint) ) {
      $_SESSION = array();
      session_destroy();
      if ($this->debug_sessions) {
        $this->add2log('sessions', __METHOD__, 'Session fixation', "session destroyed: sid: $old_sid, fingerprint: $fingerprint");
      }
      $this->debug = $this->debug2 ?: false;
      return false;
    }
    if(session_regenerate_id(true)) {
      $_SESSION['fingerprint'] = $fingerprint;
      if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, "old sid: $old_sid new sid: ".session_id()." fingerprint: $fingerprint"); }
      $this->debug = $this->debug2 ?: false;
      return true;
    } else {
      if ($this->debug_sessions) { $this->add2log('sessions', __METHOD__, 'ERROR', "could not regenerate session old sid: $old_sid fingerprint: $fingerprint"); }
      $this->debug = $this->debug2 ?: false;
      return false;
    }
  }

  /**
   * arguments: bool, cache type, 'group:name', connection name
   * example: SessionStart(1, memcache, 'group:sessions',db1);
   * in case memcache is down sessions will be writen to mysql db1
   */
  public function SessionStart($start = null, $cache = null, $group=null, $cn = null) {
    if (isset($start) and $start == 1) {
      $start = 1;
    } else {
      $sgroup = (strncmp($cache,'group:',6) == 0) ? $cache : false;
      $cn = (!$sgroup) ? $cache : $group;
      $cache = $start;
      $start = 0;
    }
    if (in_array($cache, $this->_cacheOrder)) {
      $this->dalmp_sessions_cache_type = $cache;
      $this->dalmp_sessions_cache = true;
      $sgroup = (strncmp($group,'group:',6) == 0) ? $group : false;
      $this->dalmp_sessions_group_cache = $sgroup;
      $cn = (!$sgroup) ? $group : $cn;
    } else {
      $cn = $cache;
    }
    if(strtolower($cn) == 'sqlite' && !$this->dalmp_sessions_cache) {
      $this->dalmp_sessions_cname = 'sqlite';
      $this->dalmp_sessions_sqlite_db = defined('DALMP_SESSIONS_SQLITE_DB') ? DALMP_SESSIONS_SQLITE_DB : $this->dalmp_sessions_sqlite_db;
    } else {
      $this->dalmp_sessions_cname = in_array($cn, array_keys($this->_connection)) ? $cn : $this->cname;
    }
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
        $this->add2log('sessions', __METHOD__, "start: $start cache: $this->dalmp_sessions_cache_type group: $this->dalmp_sessions_group_cache cn: $this->dalmp_sessions_cname sessions table: $this->dalmp_sessions_table");
      }
    }
  }

  public function queue($data, $queue = 'default') {
    if ($this->debug) { $this->add2log(__METHOD__, $data, $queue); }
    $queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : $this->dalmp_queue_db;
    $sdb = new SQLite3($queue_db);
    $sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $sdb->exec('CREATE TABLE IF NOT EXISTS queues (id INTEGER PRIMARY KEY, queue VARCHAR (64) NOT NULL, data TEXT, cdate DATE)');
    $sql = "INSERT INTO queues VALUES (NULL, '$queue', '".base64_encode($data)."', '".@date('Y-m-d H:i:s')."')";
    $rs = $sdb->exec($sql);
    if (!$rs) {
      trigger_error("queue: could not save $data - $queue on $queue_db", E_USER_NOTICE);
    }
  }

  public function readQueue($queue = '*', $print = false) {
    if ($this->debug) { $this->add2log(__METHOD__, $queue); }
    $queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : $this->dalmp_queue_db;
    $sdb = new SQLite3($queue_db);
    $rs = ($queue === '*') ? @$sdb->query('SELECT * FROM queues') : @$sdb->query("SELECT * FROM queues WHERE queue='$queue'");
    if ($rs) {
      if($print) {
        while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
          echo $row['id'].'|'.$row['queue'].'|'.base64_decode($row['data']).'|'.$row['cdate'].$this->isCli(1);
        }
      } else {
        return $rs;
      }
    } else {
      return array();
    }
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
      $sdb = new SQLite3($queue_db);
      $sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
      $sdb->exec('CREATE TABLE IF NOT EXISTS url (id INTEGER PRIMARY KEY, queue VARCHAR (64) NOT NULL, url VARCHAR (255) NOT NULL, expectedValue VARCHAR (255) NOT NULL, cdate DATE)');
      $sql = "INSERT INTO url VALUES (NULL, '$queue', '$url', '$expectedValue', '".@date('Y-m-d H:i:s')."')";
      $rs = $sdb->exec($sql);
      if (!$rs) {
        trigger_error("queue: could not save $url on $queue_db", E_USER_NOTICE);
      }
    } else {
      curl_close($ch);
      return true;
    }
  }

  public function readQueueURL($queue = '*', $print = false) {
    if ($this->debug) { $this->add2log(__METHOD__, $queue); }
    $queue_db = defined('DALMP_QUEUE_URL_DB') ? DALMP_QUEUE_URL_DB : $this->dalmp_queue_url_db;
    $sdb = new SQLite3($queue_db);
    $rs = ($queue === '*') ? @$sdb->query('SELECT * FROM url') : @$sdb->query("SELECT * FROM url WHERE queue='$queue'");
    if ($rs) {
      if($print) {
        while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
          echo $row['id'].'|'.$row['queue'].'|'.$row['url'].'|'.$row['expectedValue'].'|'.$row['cdate'].$this->isCli(1);
        }
      } else {
        return $rs;
      }
    } else {
      return array();
    }
  }

  public function isCli($rlb = null) {
    if ($rlb) { // return line break
      return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) ? PHP_EOL : '<br />';
    } else {
      return (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) ?: false;
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
    if (function_exists('uuid_create')) {
      $uuid = uuid_create();
      if ($this->debug) { $this->add2log(__METHOD__, $uuid); }
      return $uuid;
    } else {
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