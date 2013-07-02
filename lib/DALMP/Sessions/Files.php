<?php
namespace DALMP\Sessions;

class Files implements \SessionHandlerInterface {

  private $savePath;

  public function __construct($savePath) {
    $this->savePath = $savePath;
  }

  public function close() {
    return True;
  }

  public function destroy($session_id) {
    $file = "$this->savePath/sess_$session_id";
    if (file_exists($file)) {
      unlink($file);
    }

    return true;
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
    $this->savePath = $savePath;
    if (!is_dir($this->savePath)) {
      mkdir($this->savePath, 0777);
    }

    return true;
  }

  public function read($session_id) {
    return (string)@file_get_contents("$this->savePath/sess_$session_id");
  }

  public function write($session_id, $session_data) {
    return file_put_contents("$this->savePath/sess_$session_id", $session_data) === False ? False : True;
  }

}
