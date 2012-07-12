<?php
/**
 * RNFileSystem Bootstrap
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
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
define('CLX_SYS_ROOT', '/opt/CLx/');
 
require_once CLX_SYS_ROOT . 'CLx.php';
