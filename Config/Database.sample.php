<?php
/**
 * Reborn Database Config
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

/**
 * Database Config
 */
$Database = array(
	'development' => array(
		'type' => 'mysql',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => '',
		'pass' => '',
		'name' => 'reborn_development'
	),
	'production' => array(
		'type' => 'mysql',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => '',
		'pass' => '',
		'name' => 'reborn_production'
	),
	'test' => array(
		'type' => 'mysql',
		'host' => '127.0.0.1',
		'port' => 3306,
		'user' => '',
		'pass' => '',
		'name' => 'reborn_test'
	),
);
