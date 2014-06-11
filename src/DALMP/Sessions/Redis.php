<?php
namespace DALMP\Sessions;

/**
 * Sessions\Redis
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.1
 */
class Redis implements \SessionHandlerInterface
{
    /**
     * DALMP\Cache\Redis instance
     *
     * @access protected
     * @var DALMP\Cache\Redis
     */
    protected $cache;

    /**
     * cache_ref_key key used to store the reference on the cache engine
     *
     * @access private
     * @var string
     */
    private $cache_ref_key;

    /**
     * REF - field used for storing references
     *
     * @access private
     * @var mixed
     */
    private $dalmp_sessions_ref;

    /**
     * key used for creating more entropy when storing the sessions on a
     * key/value cache engine, useful when serving multiple sites
     *
     * @access private
     * @var mixed
     */
    private $dalmp_sessions_key;

    /**
     * constructor
     *
     * @param DALMP\Cache\Redis $cache instance
     * @param string $sessions_ref global variable to be stored as reference
     */
    public function __construct(\DALMP\Cache\Redis $cache, $sessions_ref = 'UID')
    {
        $this->cache = $cache;
        $this->dalmp_sessions_ref = defined('DALMP_SESSIONS_REF') ? DALMP_SESSIONS_REF : $sessions_ref;
        $this->dalmp_sessions_key = defined('DALMP_SESSIONS_KEY') ? DALMP_SESSIONS_KEY : __FILE__;
        $this->cache_ref_key = sprintf('DALMP_REF_%s', sha1($this->dalmp_sessions_ref . $this->dalmp_sessions_key));
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));
        if ($rs = $this->cache->Delete($key)) {

            /**
             * destroy REF on cache
             */
            if (isset($GLOBALS[$this->dalmp_sessions_ref]) && !empty($GLOBALS[$this->dalmp_sessions_ref])) {
                $this->cache->X()->HDEL($this->cache_ref_key, $key);
                $this->cache->X()->EXPIRE($this->cache_ref_key, 3600);
            }

            return true;
        } else {
            return false;
        }
    }

    public function gc($maxlifetime)
    {
        $refs = $this->cache->X()->HGETALL($this->cache_ref_key);

        $keys = array($this->cache_ref_key);

        if (is_array($refs)) {
            foreach ($refs as $key => $sref) {
                $data = explode('|', $sref);
                if (current($data) < time()) {
                    $keys[] = $key;
                }
            }

            if (count($keys) > 1) {
                $redis = $this->cache->X();
                call_user_func_array(array($redis, 'HDEL'), $keys);
                $this->cache->X()->EXPIRE($this->cache_ref_key, 3600);
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
        $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));

        return $this->cache->Get($key);
    }

    public function write($session_id, $session_data)
    {
        $ref = (isset($GLOBALS[$this->dalmp_sessions_ref]) && !empty($GLOBALS[$this->dalmp_sessions_ref])) ? $GLOBALS[$this->dalmp_sessions_ref] : null;
        $timeout = ini_get('session.gc_maxlifetime');
        $expiry = time() + $timeout;

        $key = sprintf('DALMP_%s', sha1($this->dalmp_sessions_ref . $session_id));
        $rs = (bool) $this->cache->Set($key, $session_data, $timeout);

        /**
         * store REF on cache
         */
        if ($rs && $ref) {
            return (bool) ($this->cache->X()->HSET($this->cache_ref_key, $key, sprintf('%s|%s', $ref, $expiry)) ? $this->cache->X()->EXPIRE($this->cache_ref_key, 3600) : false);
        } else {
            return $rs;
        }
    }

    /**
     * getSessionsRefs
     *
     * @return array of sessions containing any reference
     */
    public function getSessionsRefs()
    {
        $refs = $this->cache->X()->HGetALL($this->cache_ref_key);
        $rs = array();

        foreach ($refs as $key => $data) {
            list($reference, $expiry) = explode('|', $data);
            $rs[$key] = array($reference => $expiry);
        }

        return $rs;
    }

    /**
     * getSessionRef
     *
     * @param string $ref
     * @return array of session containing a specific reference
     */
    public function getSessionRef($ref)
    {
        $refs = $this->cache->X()->HGetALL($this->cache_ref_key);
        $rs = array();

        foreach ($refs as $key => $data) {
            list($reference, $expiry) = explode('|', $data);
            if ($reference == $ref) {
                $rs[$key] = array($reference => $expiry);
            }
        }

        return $rs;
    }

    /**
     * delSessionRef - delete sessions containing a specific reference
     *
     * @param string $ref
     * @return boolean
     */
    public function delSessionRef($ref)
    {
        $refs = $this->cache->X()->HGETALL($this->cache_ref_key);

        $keys = array($this->cache_ref_key);

        if (is_array($refs)) {
            foreach ($refs as $key => $data) {
                list($reference, $expiry) = explode('|', $data);
                if ($reference == $ref) {
                    $keys[] = $key;
                }
            }
        }

        if (count($keys) > 1) {
            $redis = $this->cache->X();
            call_user_func_array(array($redis, 'HDEL'), $keys);
            array_shift($keys);

            return (bool) $this->cache->delete($keys);
        } else {
            return false;
        }
    }

}
