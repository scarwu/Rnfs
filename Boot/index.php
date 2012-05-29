<?php
/**
 * Define Setting
 */
define('CLX_MODE', 'development'); // development | production | test
define('CLX_CACHE', FALSE);

/**
 * Define Default Path
 */
define('CLX_APP_ROOT', realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);
define('CLX_APP_CACHE', CLX_APP_ROOT . 'Cache' . DIRECTORY_SEPARATOR);
define('CLX_APP_CONFIG', CLX_APP_ROOT . 'Config' . DIRECTORY_SEPARATOR);
define('CLX_APP_LOGS', CLX_APP_ROOT . 'Logs' . DIRECTORY_SEPARATOR);
define('CLX_APP_VIEWS', CLX_APP_ROOT . 'Views' . DIRECTORY_SEPARATOR);

/**
 * Define CLx Core Path and Require CLx Core
 */
define('CLX_SYS_ROOT', '/opt/CLx/');
 
require_once CLX_SYS_ROOT . 'CLx.php';
