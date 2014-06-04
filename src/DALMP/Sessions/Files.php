<?php
namespace DALMP\Sessions;

/**
 * Sessions\Files
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.1
 */
class Files implements \SessionHandlerInterface
{
    /**
     * path to store sessions
     *
     * @access private
     * @var string
     */
    private $sessions_dir;

    /**
     * constructor
     *
     * @param string $dir
     */
    public function __construct($sessions_dir = false)
    {
        if (!$sessions_dir) {
            $sessions_dir = defined('DALMP_SESSIONS_DIR') ? DALMP_SESSIONS_DIR : '/tmp/dalmp_sessions';
        }

        if (!is_writable($sessions_dir)) {
            if (!is_dir($sessions_dir) && !mkdir($sessions_dir, 0700, true)) {
                throw new \InvalidArgumentException($sessions_dir . ' not accessible');
            }
        }

        $this->sessions_dir = $dir;
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $sess_path = sprintf('%s/%s/%s/%s', $this->sessions_dir, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));
        $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

        if (file_exists($sess_file)) {
            unlink($sess_file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        $session_files = $this->rsearch($this->sessions_dir, '#^.*\.sess$#');

        foreach ($session_files as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    public function open($save_path, $name)
    {
        return true;
    }

    public function read($session_id)
    {
        $sess_path = sprintf('%s/%s/%s/%s', $this->sessions_dir, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));

        if (!is_dir($sess_path) && !mkdir($sess_path, 0700, true)) {
            throw new \Exception("$sess_path  not accessible");
        }

        $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

        return (string) @file_get_contents($sess_file);
    }

    public function write($session_id, $session_data)
    {
        $sess_path = sprintf('%s/%s/%s/%s', $this->sessions_dir, substr($session_id, 0, 2), substr($session_id, 2, 2),  substr($session_id, 4, 2));

        if (!is_dir($sess_path) && !mkdir($sess_path, 0700, true)) {
            throw new \Exception("$sess_path  not accessible");
        }

        $sess_file = sprintf('%s/%s', $sess_path , "{$session_id}.sess");

        return file_put_contents($sess_file, $session_data) === false ? false : true;
    }

    /**
     * getSessionsRefs
     *
     * @param int $expiry
     * @return array of sessions containing any reference
     */
    public function getSessionsRefs($expired_sessions = false)
    {
        $refs = array();

        return false;
    }

    /**
     * getSessionRef
     *
     * @param string $ref
     * @return array of session containing a specific reference
     */
    public function getSessionRef($ref)
    {
        return false;
    }

    /**
     * delSessionsRef - delete sessions containing a specific reference
     *
     * @param string $ref
     * @return boolean
     */
    public function delSessionRef($ref)
    {
        return false;
    }

    /**
     * recursive dir search
     *
     * @param string $folder
     * @param string $pattern example: '#^.*\.sess$#'
     */
    public function rsearch($folder, $pattern)
    {
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
