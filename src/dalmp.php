<?php
namespace DALMP;

/**
 * DALMP - Database Abstraction Layer for MySQL using PHP
 *
 * @author Nicolas Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 3.0.2
 */

if (defined('DALMP_DIR')) {
    return;
} else {
    define('DALMP_DIR', dirname(__FILE__));
}

require_once DALMP_DIR . '/DALMP/Loader.php';

Loader::register();
