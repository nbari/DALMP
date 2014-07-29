<?php
namespace DALMP;

/**
 * DALMP Dependecy Injector
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.3
 */
class DI extends abstractDI
{
    public function __construct()
    {
        $this->c['database'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Database');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['cache_memcache'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Cache\Memcache');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['cache_redis'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Cache\Redis');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['cache_disk'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Cache\Disk');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['cache'] = $this->share(function($backend) {
            return new Cache($backend);
        });

        $this->c['sessions_memcache'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Sessions\Memcache');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['sessions_redis'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Sessions\Redis');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['sessions_mysql'] = $this->share(function() {
            $obj = new \ReflectionClass('DALMP\Sessions\MySQL');

            return $obj->newInstanceArgs(func_get_args());
        });

        $this->c['sessions'] = $this->share(function($backend) {
            return new Sessions($backend);
        });

    }

}
