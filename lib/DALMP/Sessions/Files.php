<?php
namespace DALMP\Sessions;

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
        $this->savePath = '/tmp';
      }
    }
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
    $file = "$this->savePath/sess_$session_id";
    if (file_exists($file)) {
      unlink($file);
    }
    return True;
  }

  public function gc($maxlifetime) {
    foreach (glob("$this->savePath/sess_*") as $file) {
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
    return (string) @file_get_contents("$this->savePath/sess_$session_id");
  }

  public function write($session_id, $session_data) {
    return file_put_contents("$this->savePath/sess_$session_id", $session_data) === False ? False : True;
  }

}
