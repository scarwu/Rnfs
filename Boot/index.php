<?php
/**
 * Bootstrap
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

/**
 * Define Setting
 */
define('CLX_MODE', 'development'); // development | production | test
define('CLX_CACHE', FALSE);

/**
 * Define Default Path
 */
define('CLX_APP_ROOT', realpath($_SERVER['DOCUMENT_ROOT'] . '/..') . '/');
define('CLX_APP_CACHE', CLX_APP_ROOT . 'Cache/');
define('CLX_APP_CONFIG', CLX_APP_ROOT . 'Config/');
define('CLX_APP_LOGS', CLX_APP_ROOT . 'Logs/');
define('CLX_APP_VIEWS', CLX_APP_ROOT . 'Views/');

/**
 * Define CLx Core Path and Require CLx Core
 */
define('CLX_SYS_ROOT', '../CLx/');
 
require_once CLX_SYS_ROOT . 'CLx.php';
