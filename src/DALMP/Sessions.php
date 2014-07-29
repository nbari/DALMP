<?php
namespace DALMP;

/**
 * Sessions
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.2
 */
class Sessions
{
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
     * @param SessionHandlerInterface $session_handler
     * @param hash_algo               $session_hash
     */
    public function __construct($session_handler = false, $hash_algo = 'sha256')
    {
        if (!$session_handler) {
            $this->session_handler = new Sessions\SQLite();
        } else {
            if ($session_handler instanceof \SessionHandlerInterface) {
                $this->session_handler = $session_handler;
            } else {
                throw new \InvalidArgumentException((string) $session_handler . ' is not an instance of SessionHandlerInterface');
            }
        }

        ini_set('session.gc_maxlifetime', defined('DALMP_SESSIONS_MAXLIFETIME') ? DALMP_SESSIONS_MAXLIFETIME : get_cfg_var('session.gc_maxlifetime'));
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.hash_bits_per_character', 5);
        ini_set('session.hash_function', in_array($hash_algo, hash_algos()) ? $hash_algo : 1);
        ini_set('session.name', 'DALMP');

        session_module_name('user');
        session_set_save_handler($this->session_handler, true);
        session_start();
    }

    /**
     * regenerate id - regenerate sessions and create a fingerprint, helps to
     * prevent HTTP session hijacking attacks.
     *
     * @param  boolean $use_IP
     * @return boolean
     */
    public function regenerate_id($use_IP = true)
    {
        $fingerprint = @$_SERVER['HTTP_ACCEPT'] . @$_SERVER['HTTP_USER_AGENT'] . @$_SERVER['HTTP_ACCEPT_ENCODING'] . @$_SERVER['HTTP_ACCEPT_LANGUAGE'];

        if ($use_IP) {
            $fingerprint .= @$_SERVER['REMOTE_ADDR'];
        }

        $fingerprint = sha1('DALMP' . $fingerprint);

        if ((isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] != $fingerprint)) {
            $_SESSION = array();
            session_destroy();
        }

        if (session_regenerate_id(true)) {
            $_SESSION['fingerprint'] = $fingerprint;

            return true;
        } else {
            return false;
        }
    }

    /**
     * to handle session Refs per session_handler
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->session_handler, $method)) {
            throw new \Exception("Undefined method {$method}");
        }

        return call_user_func_array(array($this->session_handler, $method), $args);
    }

}
