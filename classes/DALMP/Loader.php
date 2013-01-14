<?php

/**
 * DALMP_Loader - Autoloader
 *
 * git clone git://github.com/nbari/DALMP.git
 * @see http://dalmp.googlecode.com
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2
 */

class DALMP_Loader {

  public static function autoload($class) {
    if (0 !== strpos($class, 'DALMP')) {
      return;
    }

    $class_file = sprintf('%s/%s.php', DALMP_DIR . '/classes', str_replace('_', '/', $class));

    if (is_readable($class_file)) {
      require_once $class_file;
      return true;
    }
  }

  public static function register() {
    spl_autoload_register(array('DALMP_Loader', 'autoload'));
  }

}
