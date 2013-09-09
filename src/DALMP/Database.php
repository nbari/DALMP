<?php
namespace DALMP;

/**
 * Database - Abstraction Layer for MySQL
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Database {

  /**
   * Contains database object
   *
   * @access protected
   * @var object
   */
  protected $DB;

  /**
   * Contains the database parameters DSN.
   *
   * @access protected
   * @var array
   */
  protected $dsn = array();

  /**
   * For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query()
   * will return a result object.
   *
   * For other successful queries mysqli_query() will return true.
   *
   * @access protected
   * @var mixed
   */
  protected $_rs = null;

  /**
   * prepared statement object or false if an error occurred.
   *
   * @access protected
   * @var mixed
   */
  protected $_stmt = null;

  /**
   * cache DALMP\Cache instance
   *
   * @access private
   * @var mixed
   */
  public $cache = null;

  /**
   * If enabled, logs all queries and executions.
   *
   * @access private
   * @var boolean
   */
  private $debug = false;

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
  private $numOfRows;

  /**
   * Holds the num of rows affected by INSERT, UPDATE, or DELETE query.
   *
   * @access private
   * @var int
   */
  private $numOfRowsAffected;

  /**
   * Holds the num of fields returned.
   *
   * @access private
   * @var int
   */
  private $numOfFields;

  /**
   * Contains the prepared statments parameters
   *
   * @access private
   * @var array
   */
  private $stmtParams = array();

  /**
   * transaction status
   *
   * @access private
   * @var array
   */
  private $trans = array();

  /**
   * Constructor
   *
   * @param DSN $dsn
   * @param array $ssl
   */
  public function __construct($dsn = null, $ssl = null) {
    if ($dsn) {
      $dsn = parse_url($dsn);
      $this->dsn['charset'] = isset($dsn['scheme']) ? (($dsn['scheme'] == 'mysql') ? null : $dsn['scheme']) : null;
      if (isset($dsn['host'])) {
        $host = explode('=', $dsn['host']);
        if ($host[0] == 'unix_socket') {
          $this->dsn['host'] = null;
          $this->dsn['socket'] = str_replace('\\', '/', $host[1]);
        } else {
          $this->dsn['host'] = rawurldecode($dsn['host']);
          $this->dsn['socket'] = null;
        }
      } else {
        $this->dsn['host'] = '127.0.0.1';
      }
      $this->dsn['port'] = isset($dsn['port']) ? $dsn['port'] : 3306;
      $this->dsn['user'] = isset($dsn['user']) ? rawurldecode($dsn['user']) : null;
      $this->dsn['pass'] = isset($dsn['pass']) ? rawurldecode($dsn['pass']) : null;
      $this->dsn['dbName'] = isset($dsn['path']) ? rawurldecode(substr($dsn['path'], 1)) : null;
      $this->dsn['cache'] = isset($dsn['query']) ? $dsn['query'] : null;
      $this->dsn['ssl'] = $ssl;
    } else {
      throw new \InvalidArgumentException('DSN missing: charset://username:password@host:port/database');
    }
  }

  /**
   * Opens a connection to a mysql server
   * @access protected
   */
  protected function connect() {
    if ($this->DB instanceof mysqli) {
      if ($this->debug) $this->debug->log(__METHOD__, 'still connected');
      return;
    }

    if (!extension_loaded('mysqli')) {
      die('The Mysqli extension is required');
    }

    $this->DB = mysqli_init();
    mysqli_options($this->DB, MYSQLI_OPT_CONNECT_TIMEOUT, defined('DALMP_CONNECT_TIMEOUT') ? DALMP_CONNECT_TIMEOUT : 5);

    if (defined('DALMP_MYSQLI_INIT_COMMAND')) {
      mysqli_options($this->DB, MYSQLI_INIT_COMMAND, DALMP_MYSQLI_INIT_COMMAND);
    }

    if (is_array($this->dsn['ssl'])) {
      if ($this->debug) $this->debug->log('DSN - SSL', $this->dsn['ssl']);
      mysqli_ssl_set($this->DB, $this->dsn['ssl']['key'], $this->dsn['ssl']['cert'], $this->dsn['ssl']['ca'], $this->dsn['ssl']['capath'], $this->dsn['ssl']['cipher']);
    }

    $rs = @mysqli_real_connect($this->DB, $this->dsn['host'], $this->dsn['user'], $this->dsn['pass'], $this->dsn['dbName'], $this->dsn['port'], $this->dsn['socket']);
    if ($rs === false || mysqli_connect_errno()) {
      if ($this->debug) $this->debug->log(__METHOD__, 'ERROR', 'mysqli connection error');
      throw new \Exception(mysqli_connect_error(), mysqli_connect_errno());
    }

    if ($this->dsn['charset']) {
      mysqli_set_charset($this->DB, $this->dsn['charset']);
    }
  }

  /**
   * debuger
   *
   * @param boolean $log2file
   * @param mixed $debugFile
   */
  public function debug($log2file = false, $debugFile = false) {
    if ($log2file == 'off') {
      if (is_object($this->debug)) {
        $this->debug->getLog();
        $this->debug = false;
      }
    } else {
      $debugFile = $debugFile ?: (defined('DALMP_DEBUG_FILE') ? DALMP_DEBUG_FILE : '/tmp/dalmp.log');
      $this->debug = new Logger($log2file, $debugFile);
      $this->debug->log('DSN', $this->dsn);
      if ($this->isConnected()) {
        $this->debug->log('DALMP', mysqli_get_host_info($this->DB), 'protocol version: ' . mysqli_get_proto_info($this->DB), 'character set: ' . mysqli_character_set_name($this->DB));
      }
    }
    return;
  }

  /**
   * isConnected
   *
   * @return boolean
   */
  public function isConnected() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return (bool) ($this->DB instanceof mysqli);
  }

  /**
   * Closes a previously opened database connection
   */
  public function closeConnection() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return ($this->isConnected()) && $this->DB->close();
  }

  /**
   * Frees the memory associated with a result
   */
  public function Close() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return $this->_rs->close();
  }

  /**
   * Frees stored result memory for the given statement handle &
   * Closes a prepared statement
   */
  public function PClose() {
    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__);
    $this->_stmt->free_result();
    return $this->_stmt->close();
  }

  /**
   * getNumOfRows
   *
   * @return int num of rows
   */
  public function getNumOfRows() {
    return $this->numOfRows;
  }

  /**
   * getNumOfRowsAffected
   *
   * @return int num of rows affected
   */
  public function getNumOfRowsAffected() {
    return $this->numOfRowsAffected;
  }

  /**
   * getNumOfFields
   *
   * @return int num of fields
   */
  public function getNumOfFields() {
    return $this->numOfFields;
  }

  /**
   * Get the column names
   *
   * @param $table;
   * @return array or false if no table set
   */
  public function getColumnNames($table = null) {
    return ($table) ? $this->getCol("DESCRIBE $table") : false;
  }

  /**
   * Sets the Fetch Mode
   *
   * @chainable
   * @param ASSOC = MYSQLI_ASSOC, NUM = MYSQLI_NUM, null = MYSQLI_BOTH.
   */
  public function FetchMode($mode = null) {
    switch (strtoupper($mode)) {
    case 'NUM':
      $this->fetchMode = MYSQLI_NUM;
      break;

    case 'ASSOC':
      $this->fetchMode = MYSQLI_ASSOC;
      break;

    default :
      $this->fetchMode = MYSQLI_BOTH;
    }

    if ($this->debug) $this->debug->log(__METHOD__, $mode, $this->fetchMode);

    return $this;
  }

  /**
   * Prepare arguments
   *
   * @param string $args
   * @return array with arguments;
   */
  public function Prepare() {
    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, func_get_args());

    switch (func_num_args()) {
    case 1:
      $param = func_get_arg(0);
      $clean = true;
      break;

    case 2:
      $key = func_get_arg(0);
      $param = func_get_arg(1);
      if (in_array($key, array('i', 'd', 's', 'b'), true)) {
        $this->stmtParams[] = array($key => $param);
      } else {
        $clean = true;
      }
      break;

    default :
      return $this->stmtParams;
    }

    if (isset($clean)) {
      if (is_numeric($param)) {
        $param = !strcmp(intval($param), $param) ? (int) $param : (!strcmp(floatval($param), $param) ? (float) $param : $param);
      }
      $key = is_int($param) ? 'i' : (is_float($param) ? 'd' : (is_string($param) ? 's' : 'b'));
      return $this->stmtParams[] = array($key => $param);
    }
  }

  /**
   * Prepared Statements
   *
   * example: PGetAll('SELECT * FROM users WHERE name=? AND id=?', 'name', 1, 'db1')
   * user also can define  the corresponding type of the bind variables (i, d, s, b): http://pt.php.net/manual/en/mysqli-stmt.bind-param.php
   * example: PGetAll('SELECT * FROM table WHERE name=? AND id=?', array('s'=>'99.3', 7)); or use the Prepare() method
   *
   * @param SQL $sql
   * @param string $params
   */
  public function PExecute() {
    $args = func_get_args();
    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, $args);

    (!$this->isConnected()) && $this->connect();

    $sql = array_shift($args);
    $this->_stmt = $this->DB->prepare($sql);

    if (!$this->_stmt) {
      $this->closeConnection();
      trigger_error('ERROR -> ' . __METHOD__ . ": Please check your sql statement, unable to prepare: $sql with args: " . json_encode($args), E_USER_ERROR);
    }

    $params = array();
    $types = null;

    $args = is_array(current($args)) ? current($args) : $args;

    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, 'args:',$args);

    if (!empty($args)) {

      foreach ($args as $key => $param) {
        $params[] = &$args[$key];

        if (!in_array($key, array('i', 'd', 's', 'b'), true)) {

          if (is_numeric($param)) {
            $param = !strcmp(intval($param), $param) ? (int) $param : (!strcmp(floatval($param), $param) ? (float) $param : $param);
          }

          $key = is_int($param) ? 'i' : (is_float($param) ? 'd' : (is_string($param) ? 's' : 'b'));
        }

        if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, "key: $key param: $param");
        $types .= $key;
      }

      array_unshift($params, $types);

      if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, "sql: $sql params:", $params);

      call_user_func_array(array($this->_stmt, 'bind_param'), $params);
    }

    /**
     * if you get erros like 'Illegal mix of collations
     * (latin1_swedish_ci,IMPLICIT) and (utf8_general_ci,COERCIBLE)'
     * try to set your table fiels to: "character set: UTF8"
     * and "collation: utf8_unicode_ci"
     */
    if ($this->_stmt->execute()) {
      $this->_stmt->store_result();
      if (is_object($this->_stmt->result_metadata())) {
        $this->numOfRows = $this->_stmt->num_rows;
        $this->numOfFields = $this->_stmt->field_count;
        if (!$this->_stmt->num_rows) {
          return false;
        }
      }

      $this->numOfRowsAffected = $this->_stmt->affected_rows;

      /**
       * An integer greater than zero indicates the number of rows affected
       * or retrieved. Zero indicates that no records where updated for an
       * UPDATE/DELETE statement, no rows matched the WHERE clause in the query
       * or that no query has yet been executed. -1 indicates that the query has
       * returned an error. NULL indicates an invalid argument was supplied to the
       * function.
       */
      if ($this->_stmt->affected_rows > 0) {
        return true;
      } elseif ($this->_stmt->affected_rows == -1) {
        return false;
      } else {
        return $this->_stmt->affected_rows;
      }
    } else {
      if (array_key_exists('error', $this->trans)) {
        $this->trans['error']++;
      }

      if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, 'ERROR', "sql: $sql  params: ", $params, " Errorcode:" . $this->DB->errno);

      throw new \ErrorException(__METHOD__ . 'ERROR -> ' . $this->DB->error . " - sql: $sql with params: " . json_encode($params));
    }
  }

  /**
   * Prepared Statements query
   *
   * @param array $row
   */
  public function Pquery(&$row) {
    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__);
    $meta = $this->_stmt->result_metadata();
    $columns = array();

    while ($column = $meta->fetch_field()) {
      $columns[] = &$row[$column->name];
    }
    call_user_func_array(array($this->_stmt, 'bind_result'), $columns);

    return $this->_stmt->fetch();
  }

  /**
   * _pFetch
   *
   * @access protected
   * @return array
   */
  protected function _pFetch($get = null) {
    if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__, $get);

    if (!$this->_stmt->num_rows) {
      $this->PClose();
      return false;
    }

    $meta = $this->_stmt->result_metadata();
    $columns = array();
    $results = array();

    while (($column = $meta->fetch_field()) !== false) {
      $columns[$column->name] = &$results[$column->name];
    }

    call_user_func_array(array($this->_stmt, 'bind_result'), $columns);

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
        if ($this->debug) { $this->debug->log('PreparedStatements', __METHOD__, 'ERROR', $get, 'num of columns < 2'); }
          return false;
      }
      if ($this->numOfFields == 2) {
        while ($this->_stmt->fetch()) {
          $rs[reset($columns)] = next($columns);
        }
      } else {
        while ($this->_stmt->fetch()) {
          $rs[reset($columns)] = array_slice($columns, 1);
        }
      }
      break;

    default :
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

          default :
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
    }

    $this->PClose();
    return $rs;
  }

  /**
   * _fetch
   *
   * @access protected
   * @return an array of strings that corresponds to the fetched row or null
   * if there are no more rows in resultset.
   */
  protected function _fetch() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return $this->_rs->fetch_array($this->fetchMode);
  }

  /**
   * Auto Execute
   *
   * @param string $table
   * @param array $fields
   * @param string $mode
   * @param string $where
   * @return true or false on error
   */
  public function AutoExecute($table, array $fields, $mode = 'INSERT', $where = null) {
    if ($this->debug) $this->debug->log(__METHOD__, 'args:', $table, $fields, $mode, $where);

    $mode = (strtoupper($mode) == 'INSERT') ? 'INSERT' : 'UPDATE';

    if ($mode == 'UPDATE' && !$where) {
      if ($this->debug) $this->debug->log( __METHOD__, 'ERROR', 'WHERE clause missing');
      throw new \InvalidArgumentException(__METHOD__ . ' WHERE clause missing');
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
      if (empty($data)) {
        if ($this->debug) $this->debug->log(__METHOD__, 'ERROR', "no matching fields on table: $table with fields:", $fields);
        return false;
      }
    } else {
      return false;
    }

    if ($mode == 'INSERT') {
      $fields = implode(', ', array_keys($data));
      $placeholder = rtrim($placeholder, ',');
      $query = array_values($data);
      $sql = "INSERT INTO $table ($fields) VALUES($placeholder)";
    } else {
      $fields = implode('=?, ', array_keys($data));
      $fields.= '=?';
      $query = array_values($data);
      $sql = "UPDATE $table SET $fields WHERE $where";
    }
    return call_user_func_array(array($this, 'PExecute'), array($sql, $query));
  }

  /**
   * multiple insert
   *
   * @param string $table
   * @param array $col_name example array('col1', 'col2')
   * @param array $multiple_values example array(array('val1', 'val2'))
   * @return boolean
   */
  public function multipleInsert($table, array $col_name, array $multiple_values) {
    $num_of_fields = count($col_name);
    if ($num_of_fields != count(end($multiple_values))) {
      throw new \InvalidArgumentException('number of values do not match number of columns');
    }

    $pvalues = '';
    $values = array();

    foreach ($multiple_values as $value) {
      $placeholder ='(';
      for ($i = 0; $i < $num_of_fields; $i++) {
        $values[] = isset($value[$i]) ? $value[$i] : null;
        $placeholder .= '?,';
      }
      $pvalues .= rtrim($placeholder, ',') . '),';
      $placeholder = null;
    }

    $pvalues = rtrim($pvalues,',');

    $columns = array_map(create_function('$n', 'return "`$n`";'), $col_name);

    $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES $pvalues";

    if ($this->debug) $this->debug->log(__METHOD__, $sql, $values);

    return call_user_func_array(array($this, 'PExecute'), array($sql, $values));
  }

  /**
   * Execute SQL statement
   *
   * @param strign $sql
   * @return true or false if there was an error in executing the sql.
   */
  public function Execute($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");

    (!$this->isConnected()) && $this->connect();

    if ($rs = $this->DB->query($sql)) {
      if (is_object($rs)) {
        $this->_rs = $rs;
        $this->numOfRows = $this->_rs->num_rows;
        $this->numOfFields = $this->_rs->field_count;
        if ($this->debug) $this->debug->log(__METHOD__, 'returned object', "#rows: $this->numOfRows #fields: $this->numOfFields");
        if (!$this->numOfRows) {
          $this->Close();
          return false;
        }
      }

      $this->numOfRowsAffected = $this->DB->affected_rows;

      /**
       * An integer greater than zero indicates the number of rows affected or
       * retrieved. Zero indicates that no records were updated for an UPDATE
       * statement, no rows matched the WHERE clause in the query or that no query
       * has yet been executed. -1 indicates that the query returned an error.
       */
      if ($this->DB->affected_rows > 0) {
        return true;
      } elseif ($this->DB->affected_rows == -1) {
        return false;
      } else {
        return $this->DB->affected_rows;
      }
    } else {
      if (array_key_exists('error', $this->trans)) {
        $this->trans['error']++;
      }
      if ($this->debug) $this->debug->log(__METHOD__, 'ERROR', "sql: $sql Errorcode: " . $this->DB->errno);
      throw new \ErrorException(__METHOD__ . ' ERROR -> ' . $this->DB->error . " - sql: $sql");
    }
  }

  /**
   * Query
   *
   * @see _fetch
   * @return array or null
   */
  public function query() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return $this->_fetch();
  }

  /**
   * Export to CSV
   *
   * @param string $sql
   * @return csv
   */
  public function csv() {
    $args = func_get_args();
    if ($this->debug) $this->debug->log(__METHOD__, $args);

    switch (func_num_args()) {
    case 1:
      if (call_user_func_array(array($this, 'Execute'), $args)) {
        $row = $this->_rs->fetch_array(MYSQLI_ASSOC);
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($row));
        $this->_rs->data_seek(0);
        while ($row = $this->_rs->fetch_array(MYSQLI_NUM)) {
          fputcsv($fp, $row);
        }
        $this->Close();
        fclose($fp);
      }
      break;

    default:
      if (call_user_func_array(array($this, 'PExecute'), $args)) {
        $meta = $this->_stmt->result_metadata();
        $columns = array();
        $results = array();
        while (($column = $meta->fetch_field()) !== false) {
          $columns[$column->name] = &$results[$column->name];
        }
        $fp = fopen('php://output', 'w');
        fputcsv($fp, array_keys($columns));
        call_user_func_array(array($this->_stmt, 'bind_result'), $columns);
        while ($this->_stmt->fetch()) {
          fputcsv($fp, $columns);
        }
        $this->PClose();
        fclose($fp);
      }
    }
  }

  /**
   * maps the result to an object
   *
   * @param sting sql the query string
   * @param string class_name of the class to instantiate
   * @param array optional array of parameters to pass to the constructor for class_name objects.
   * @see mysqli_result::fetch_object
   * @return object
   */
  public function map($sql, $class_name=null, $params=array()) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
      return ($class_name) ? $this->_rs->fetch_object($class_name, $params) : $this->_rs->fetch_object();
    } else {
      return false;
    }
  }

  /**
   * Fetch a result row as an associative, a numeric array, or both
   *
   * @param SQL $sql
   * @return array or false
   */
  public function getAll($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
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

  /**
   * Get a Row
   *
   * @param SQL $sql
   * @return the first row as an array or false.
   */
  public function getRow($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
      $row = $this->_fetch();
      $this->Close();
      return $row;
    } else {
      return false;
    }
  }

  /**
   * Get a Column
   *
   * @param SQL $sql
   * @return the first column as an array, or false.
   */
  public function getCol($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
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

  /**
   * Get One
   *
   * @param SQL $sql
   * @return the first field of the first row, or false.
   */
  public function getOne($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
      $row = $this->_rs->fetch_row();
      $this->Close();
      return reset($row);
    } else {
      return false;
    }
  }

  /**
   * Get an associative array using the first column as keys
   *
   * @param SQL $sql
   * @return associative array, false if columns < 2, or no records found.
   */
  public function getASSOC($sql) {
    if ($this->debug) $this->debug->log(__METHOD__, "sql: $sql");
    if ($this->Execute($sql)) {
      $cols = $this->numOfFields;
      if ($cols < 2) {
        return false;
      }
      $this->fetchMode = MYSQLI_ASSOC;
      $assoc = array();
      if ($cols == 2) {
        while ($row = $this->_fetch()) {
          $assoc[reset($row)] = next($row);
        }
      } else {
        while ($row = $this->_fetch()) {
          $assoc[reset($row)] = array_slice($row, 1);
        }
      }
      $this->Close();
      return $assoc;
    } else {
      return false;
    }
  }

  /**
   * Start the transaction
   */
  public function StartTrans() {
    if (empty($this->trans)) {
      if ($this->debug) $this->debug->log('transactions', __METHOD__, $this->trans);
      $this->trans = array('level' => 0, 'error' => 0);
      return $this->Execute('BEGIN');
    } else {
      $this->trans['level']++;
      if ($this->debug) $this->debug->log('transactions', __METHOD__, array('transaction level' => $this->trans['level']));
      return $this->Execute(sprintf('SAVEPOINT level%d', $this->trans['level']));
    }
  }

  /**
   * Complete the transaction
   *
   * @return boolean
   */
  public function CompleteTrans() {
    if ($this->debug) $this->debug->log('transactions', __METHOD__, $this->trans);
    if (empty($this->trans)) {
      return false;
    } else {
      if ($this->trans['error'] > 0) {
        if ($this->debug) $this->debug->log('transactions', __METHOD__, 'ERROR', array('error in level' => $this->trans['level']));
        if ($this->trans['level'] > 0) {
          $this->Execute(sprintf('ROLLBACK TO SAVEPOINT level%d', $this->trans['level']));
          $this->trans['level']--;
        } else {
          $this->Execute('ROLLBACK');
        }
        return false;
      }
      if ($this->trans['level'] == 0) {
        $this->trans = array();
        return $this->Execute('COMMIT');
      } else {
        $rs = $this->Execute(sprintf('RELEASE SAVEPOINT level%d', $this->trans['level']));
        $this->trans['level']--;
        return $rs;
      }
    }
  }

  /**
   * Rollback the transaction
   *
   * @return false if there was an error executing the ROLLBACK.
   */
  public function RollBackTrans() {
    if ($this->debug) $this->debug->log('transactions', __METHOD__, $this->trans);
    if (isset($this->trans['level']) && $this->trans['level'] > 0) {
      $rs = $this->Execute(sprintf('ROLLBACK TO SAVEPOINT level%d', $this->trans['level']));
      $this->trans['level']--;
      return $rs;
    } else {
      return $this->Execute('ROLLBACK');
    }
  }

  /**
   * Insert_Id
   *
   * @return int the auto generated id used in the last query
   */
  public function Insert_Id() {
    if ($this->debug) $this->debug->log(__METHOD__);
    return $this->DB->insert_id;
  }

  /**
   * ErrorMsg
   *
   * @return string description of the last error
   */
  public function ErrorMsg() {
    return $this->DB->error;
  }

  /**
   * ErrorNum
   *
   * @return int error code
   */
  public function ErrorNum() {
    return $this->DB->errno;
  }

  /**
   * Quotes a string
   *
   * @param string $value
   */
  public function qstr($value) {
    if ($this->debug) $this->debug->log(__METHOD__, func_get_args());
    if (is_int($value) || is_float($value)) {
      $rs = $value;
    } else {
      (!$this->isConnected()) && $this->connect();
      $rs = $this->DB->real_escape_string($value);
    }
    if ($this->debug) $this->debug->log(__METHOD__, "returned: $rs");
    return $rs;
  }

  /**
   * renumber
   *
   * @param string $table
   * @param int $id
   */
  public function renumber($table, $row = 'id') {
    if (isset($table)) {
      return $this->Execute('SET @var_dalmp=0') ? ($this->Execute("UPDATE $table SET $row = (@var_dalmp := @var_dalmp +1)") ? $this->Execute("ALTER TABLE $table AUTO_INCREMENT = 1") : false) : false;
    } else {
      return false;
    }
  }

  /**
   * useCache
   *
   * helps to follow the single responsibility principle
   *
   * @param DALMP\Cache $cache
   */
  public function useCache(Cache $cache) {
    if ($this->debug) $this->debug->log('Cache', $cache);
    $this->cache = $cache;
  }

  /**
   * general method for caching
   *
   * @param string $fetch_method
   * @param int $expire
   * @param string $sql
   * @param string $key
   * @param string $group
   * @return boolean;
   */
  protected function _Cache() {
    $args = func_get_args();

    if ($this->debug) $this->debug->log(__METHOD__, 'Args', $args);

    $fetch = array_shift($args);
    $expire = (int) (reset($args)) ? array_shift($args) : 3600;
    $sql = array_shift($args);
    $key = isset($args[0]) ? $args[0] : $fetch;

    if (strncmp($key, 'group:', 6) == 0) {
      $group = $key;
      $key = $fetch;
    } else {
      $group = (isset($args[1]) and (strncmp($args[1], 'group:', 6) == 0)) ? $args[1] : null;
    }

    $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
    $hkey = sha1($skey . $sql . $key);

    if ($this->debug) $this->debug->log(__METHOD__, 'Parsed Args', array('fetch method' => $fetch, 'expire' => $expire, 'sql' => $sql, 'key' => $key, 'group' => $group), array('Cache key' => $hkey));

    if ($this->cache instanceof Cache && $cache = $this->cache->get($hkey)) {
      return $cache;
    } else {
      switch ($fetch) {
      case 'all':
        $cache = $this->getAll($sql);
        break;
      case 'row':
        $cache = $this->getRow($sql);
        break;
      case 'col':
        $cache = $this->getCol($sql);
        break;
      case 'one':
        $cache = $this->getOne($sql);
        break;
      case 'assoc':
        $cache = $this->getASSOC($sql);
        break;
      }

      if ($this->cache instanceof Cache) {
        $this->_setCache($hkey, $cache, $expire, $group);
      } else {
        trigger_error('Cache instance not defined, use the method useCache($cache) to set a cache engine.', E_USER_WARNING);
      }

      if ($this->debug) $this->debug->log(__METHOD__, 'Set', array('key' => $hkey, 'expire' => $expire, 'group' => $group));

      return $cache;
    }
  }

  /**
   * method for caching prepared statements
   *
   * @param string $fetch_method
   * @param int $expire
   * @param string $sql
   * @param string $key
   * @param string $group
   * @return boolean;
   */
  protected function _CacheP() {
    $args = func_get_args();

    if ($this->debug) $this->debug->log(__METHOD__, 'Args', $args);

    $fetch = array_shift($args);
    $expire = (int) (reset($args)) ? array_shift($args) : 3600;
    $sql = array_shift($args);

    // expected params
    $eparams = count(explode('?', $sql, -1));
    $targs = count($args);
    $args = is_array(current($args)) ? current($args) : $args;
    if ($targs > $eparams) {
      if (($targs - $eparams) == 1) {
        $key = array_pop($args);
        $params = $args;
        if (strncmp($key, 'group:', 6) == 0) {
          $group = $key;
          $key = $fetch . implode('|', array_merge(array_keys($args), $args));
        } else {
          $group = null; // only key no group
        }
      } else {
        $group = array_pop($args);
        $group = (strncmp($group, 'group:', 6) == 0) ? $group : null;
        $key = array_pop($args);
        $params = $args;
      }
    } else {
      $key = $fetch . implode('|', array_merge(array_keys($args), $args));
      $params = $args;
      $group = null;
    }

    array_unshift($args, $sql);

    $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
    $hkey = sha1($skey . $sql . $key);

    if ($this->debug) $this->debug->log(__METHOD__, 'Parsed Args', array('fetch method' => $fetch, 'expire' => $expire, 'sql' => $sql, 'key' => $key, 'group' => $group), array('Cache key' => $hkey));

    if ($this->cache instanceof Cache && $cache = $this->cache->Get($hkey)) {
      return $cache;
    } else {
      $nargs = array();
      foreach (array_keys($args) as $akey) {
        if (!is_int($akey)) {
          $nargs['dalmp'][$akey] = $args[$akey];
        } else {
          $nargs[] = $args[$akey];
        }
      }
      call_user_func_array(array($this, 'PExecute'), $nargs);
      $cache = $this->_pFetch($fetch);

      if ($this->cache instanceof Cache) {
        $this->_setCache($hkey, $cache, $expire, $group);
      } else {
        trigger_error('Cache instance not defined, use the method useCache($cache) to set a cache engine.', E_USER_WARNING);
      }

      if ($this->debug) $this->debug->log(__METHOD__, 'Set', array('key' => $hkey, 'expire' => $expire, 'group' => $group));

      return $cache;
    }
  }

  /**
   * _setCache - store data in cache
   *
   * @access protected
   * @param string $hkey The key that will be associated with the item.
   * @param data $cache The variable to store
   * @param int $expire Expiration time of the item
   * @param string $group group:name (to group cache keys) usefull when flushing the cache
   * @return boolean
   */
  protected function _setCache($hkey, $cache, $expire = 3600, $group = null) {
    if ($group) {
      $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
      $gkey = sha1($skey . $group);

      if ($gCache = $this->cache->Get($gkey)) {
        foreach ($gCache as $key => $exp) {
          if ($exp < time()) {
            unset($gCache[$key]);
          }
        }
      } else {
        $gCache = array();
      }

      $gCache[$hkey] = time() + $expire;

      if (!$this->cache->Set($hkey, $cache, $expire)->Set($gkey, $gCache, $expire)) {
        throw new \UnexpectedValueException('Can not store data on cache');
      }
    } else {
      if (!$this->cache->Set($hkey, $cache, $expire)) {
        throw new \UnexpectedValueException('Can not store data on cache');
      }
    }
    return true;
  }

  /**
   * Cache flush
   *
   * @param string $sql, SQL, cache group or null
   * @param string $key
   * @return boolean
   */
  public function CacheFlush($sql = null, $key = null) {
    if (is_null($sql)) {
      if ($this->debug) $this->debug->log(__METHOD__, 'Flushing all cache');
      return $this->cache->Flush();
    }

    $skey = defined('DALMP_SITE_KEY') ? DALMP_SITE_KEY : 'DALMP';
    $hkey = sha1($skey . $sql . $key);

    if (strncmp($sql, 'group:', 6) == 0) {
      $gkey = sha1($skey . $sql);
      $group = $this->cache->get($gkey);
      $group = is_array($group) ? $group : array();
      if ($this->debug) $this->debug->log(__METHOD__, 'group', array('group' => $sql, 'Cache group key' => $gkey));
      foreach ($group as $key => $timeout) {
        $this->cache->Delete($key);
      }
    }

    if ($this->debug) $this->debug->log(__METHOD__, 'Delete', array('sql' => $sql, 'key' => $hkey));

    return $this->cache->Delete($hkey);
  }

  /**
   * @return string server Version
   */
  public function getServerVersion() {
    $version = $this->DB->server_version;
    $major = (int) ($version / 10000);
    $minor = (int) ($version % 10000 / 100);
    $revision = (int) ($version % 100);
    return $major . '.' . $minor . '.' . $revision;
  }

  /**
   * @return string Client Version
   */
  public function getClientVersion() {
    $version = $this->DB->client_version;
    $major = (int) ($version / 10000);
    $minor = (int) ($version % 10000 / 100);
    $revision = (int) ($version % 100);
    return $major . '.' . $minor . '.' . $revision;
  }

  /**
   * isCli()
   *
   * @param boolean $eol
   * @return boolean or PHP_EOL, <br/>
   */
  public static function isCli($eol = null) {
    ($cli = (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']))) && $cli = $eol ? PHP_EOL : true;
    return $cli ?  : ($eol ? '<br/>' : false);
  }

  /**
   * Universally Unique Identifier v4
   *
   * @param int $b
   * @return UUID, if $b returns binary(16)
   */
  public static function UUID($b=null) {
    if (function_exists('uuid_create')) {
      $uuid = uuid_create();
    } else {
      $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
    }
    return $b ? pack('H*', str_replace('-', '', $uuid)) : $uuid;
  }

  /**
   * sqlite3 Queue
   *
   * @param SQL $data
   * @param string $queue
   */
  public static function Queue($data, $queue = 'default') {
    $queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : DALMP_DIR.'/dalmp_queue.db';
    $sdb = new SQLite3($queue_db);
    $sdb->busyTimeout(2000);

    if (defined('DALMP_SQLITE_ENC_KEY')) $sdb->exec("PRAGMA key='" . DALMP_SQLITE_ENC_KEY . "'");

    $sdb->exec('PRAGMA synchronous=OFF; PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY');
    $sdb->exec('CREATE TABLE IF NOT EXISTS queues (id INTEGER PRIMARY KEY, queue VARCHAR (64) NOT null, data TEXT, cdate DATE)');
    $sql = "INSERT INTO queues VALUES (null, '$queue', '" . base64_encode($data) . "', '" . @date('Y-m-d H:i:s') . "')";

    if (!$sdb->exec($sql)) {
      trigger_error("queue: could not save $data - $queue on $queue_db", E_USER_NOTICE);
    }

    $sdb->busyTimeout(0);
    $sdb->close();
  }

  /**
   * read the sqlite3 queue
   *
   * @param string queue name
   * @param int print or return the queue
   * @return true or array
   */
  public static function readQueue($queue = '*', $print = false) {
    $queue_db = defined('DALMP_QUEUE_DB') ? DALMP_QUEUE_DB : DALMP_DIR . '/dalmp_queue.db';
    $sdb = new SQLite3($queue_db);

    if (defined('DALMP_SQLITE_ENC_KEY')) $sdb->exec("PRAGMA key='" . DALMP_SQLITE_ENC_KEY . "'");

    $rs = ($queue === '*') ? @$sdb->query('SELECT * FROM queues') : @$sdb->query("SELECT * FROM queues WHERE queue='$queue'");

    if ($rs) {
      if ($print) {
        while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
          echo $row['id'] , '|' , $row['queue'] , '|' , base64_decode($row['data']) , '|' , $row['cdate'] , DALMP::isCli(1);
        }
      } else {
        return $rs;
      }
    } else {
      return array();
    }
  }

  /**
   * magic method for Pget, Cacheget, and CachePge(all, row, col, one, assoc)
   */
  public function __call($name, $args) {
    $n = strtolower($name);

    $method = function ($subject) {
      ($m = preg_match('/^(pget|cacheget|cachepget)/i', $subject, $matches)) && $m = $matches[0];
      return $m;
    };

    $get = function ($m) use ($n) {
      $method = explode($m, $n) + array(null, null);
      return in_array($method[1], array('all', 'row', 'col', 'one', 'assoc')) ? $method[1] : false;
    };

    switch ($method($n)) {
    case 'pget':
      if ($func = $get('pget')) {
        if ($this->debug) $this->debug->log('PreparedStatements', __METHOD__);
        return call_user_func_array(array($this, 'PExecute'), $args) ? $this->_pFetch($func) : false;
      }
      break;

    case 'cacheget':
      if ($func = $get('cacheget')) {
        if ($this->debug) $this->debug->log('Cache', __METHOD__);
        array_unshift($args, $func);
        return call_user_func_array(array($this, '_Cache'), $args);
      }
      break;

    case 'cachepget':
      if ($func = $get('cachepget')) {
        if ($this->debug) $this->debug->log('CacheP', __METHOD__);
        array_unshift($args, $func);
        return call_user_func_array(array($this, '_CacheP'), $args);
      }
      break;
    }
    throw new \Exception("DALMP DB method ({$name}) does not exist", 0);
  }

  /**
   * usage: echo $db;
   *
   * @return DALMP stats
   */
  public function __toString() {
    if ($this->isConnected()) {
      $status = 'DALMP :: ';
      $status .= 'Character set: ' . $this->DB->character_set_name();
      $status .= ', ' . $this->DB->host_info;
      $status .= ', Server version: ' . $this->getServerVersion();
      $status .= ', Client version: ' . $this->getClientVersion();
      $status .= ', System status: ' . $this->DB->stat();
    } else {
      $status = 'no connections available';
    }

    if ($this->debug) $this->debug->log(__METHOD__, $status);

    return $status;
  }

  /**
   * DALMP destructor
   */
  public function __destruct() {
    if ($this->debug) $this->debug->getLog();
    return $this->closeConnection();
  }

}
