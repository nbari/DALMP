<?php
namespace DALMP;

class Sessions {

  /**
   * session_handler
   *
   * @access private
   * @SessionHandlerInterface
   */
  private $session_handler;

  /**
   * construct - set the sesion save handler
   *
   * @param SessionHandlerInterface object
   */
  public function __construct() {
    $args = func_get_args();
    if (empty($args)) {
      $this->session_handler = new DALMP\Sessions\SQLite();
    } else {
      if ($args[0] instanceof \SessionHandlerInterface) {
        $this->session_handler = $args[0];
      } else {
        throw new \InvalidArgumentException((string) $args[0] . ' is not an intance of SessionHandlerInterface');
      }
    }

    session_module_name('user');
    session_set_save_handler($this->session_handler, True);

    ini_set('session.gc_maxlifetime', defined('DALMP_SESSIONS_MAXLIFETIME') ? DALMP_SESSIONS_MAXLIFETIME : get_cfg_var('session.gc_maxlifetime'));
    ini_set('session.name', 'DALMP');
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_trans_sid', 0);
    ini_set('session.hash_function', 'sha256');
    ini_set('session.hash_bits_per_character', 5);
    @ini_set('session.entropy_length', '128');

    session_start();
  }

  public function __call($method, $args) {
    if (!method_exists($this->session_handler, $method)) {
      throw new \Exception("Undefined method {$method}");
    }

    return call_user_func_array(array($this->session_handler, $method), $args);

  }

}
