<?php
namespace DALMP\Sessions;

/**
 * Sessions Files class - Store sessions on disk files
 *
 * Pending work for storing references, possible approach is to use something
 * similar on engines like memcache / redis
 */
class Files implements \SessionHandlerInterface {

  /**
   * path to store sessions
   *
   * @access private
   * @var string
   */
  private $savePath;

  /**
   * constructor
   *
   * @param $savePath string
   */
  public function __construct($savePath = null) {
    if ($savePath) {
      if (!is_writable($savePath)) {
        if (!is_dir($savePath) && !mkdir($savePath, 0700, True)) {
          throw new \InvalidArgumentException($savePath . ' not accessible');
        }
      }
      $this->savePath = $savePath;
    } else {
      if (!is_writable('/tmp')) {
        if (!is_dir('/tmp') && !mkdir('/tmp', 0700, True)) {
          throw new \Exception('/tmp  not accessible');
        }
      } else {
        $this->savePath = '/tmp/dalmp_sessions';
      }
    }
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
    $sess_path = sprintf('%s/%s/%s/%s', $this->savePath, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));
    $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

    if (file_exists($sess_file)) {
      unlink($sess_file);
    }

    return True;
  }

  public function gc($maxlifetime) {
    $session_files = $this->rsearch($this->savePath, '#^.*\.sess$#');

    foreach ($session_files as $file) {
      if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
        unlink($file);
      }
    }

    return True;
  }

  public function open($save_path, $name) {
    return True;
  }

  public function read($session_id) {
    $sess_path = sprintf('%s/%s/%s/%s', $this->savePath, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));

    if (!is_dir($sess_path) && !mkdir($sess_path, 0700, True)) {
      throw new \Exception("$sess_path  not accessible");
    }

    $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

    return (string) @file_get_contents($sess_file);
  }

  public function write($session_id, $session_data) {
    $sess_path = sprintf('%s/%s/%s/%s', $this->savePath, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));

    if (!is_dir($sess_path) && !mkdir($sess_path, 0700, True)) {
      throw new \Exception("$sess_path  not accessible");
    }

    $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

    return file_put_contents($sess_file, $session_data) === False ? False : True;
  }

  /**
   * getSessionsRefs
   *
   * @param int $expiry
   * @return array of sessions containing any reference
   */
  public function getSessionsRefs($expired_sessions = False) {
    $refs = array();
    return False;
  }

  /**
   * getSessionRef
   *
   * @param string $ref
   * @return array of session containing a specific reference
   */
  public function getSessionRef($ref) {
    return False;
  }

  /**
   * delSessionsRef - delete sessions containing a specific reference
   *
   * @param string $ref
   * @return boolean
   */
  public function delSessionRef($ref) {
    return False;
  }

  /**
   * recursive dir search
   *
   * @param string $folder
   * @param string $pattern example: '#^.*\.sess$#'
   */
  public function rsearch($folder, $pattern) {
    $dir = new RecursiveDirectoryIterator($folder);
    $ite = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
    $fileList = array();
    foreach ($files as $file) {
      $fileList = array_merge($fileList, $file);
    }
    return $fileList;
  }

}
