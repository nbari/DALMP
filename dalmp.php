<?php

/**
 * DALMP - Database Abstraction Layer for MySQL using PHP
 *
 * git clone git://github.com/nbari/DALMP.git
 * @see http://dalmp.googlecode.com
 *
 * @author Nicolas de Bari Embriz <nbari@dalmp.com>
 * @package DALMP
 * @license BSD License
 * @version 2.1
 */

if (!defined('DALMP_DIR')) define('DALMP_DIR', dirname(__FILE__));

require_once DALMP_DIR . '/classes/DALMP/Loader.php';

DALMP_Loader::register();
