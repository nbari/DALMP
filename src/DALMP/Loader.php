<?php
namespace DALMP;

/**
 * Loader - Autoloader
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0
 */
class Loader
{
    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName = DALMP_DIR . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if (is_readable($fileName)) {
            require $fileName;
        }
    }

    public static function register()
    {
        spl_autoload_register(array('DALMP\Loader', 'autoload'));
    }

}
