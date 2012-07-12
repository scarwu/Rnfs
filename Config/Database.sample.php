<?php
/**
 * RNFileSystem Database Config
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

/**
 * Database Config
 */
$Database['development'] = array(
	'type' => 'mysql',
	'host' => '127.0.0.1',
	'port' => 3306,
	'user' => '',
	'pass' => '',
	'name' => 'reborn_development'
);

$Database['production'] = array(
	'type' => 'mysql',
	'host' => '127.0.0.1',
	'port' => 3306,
	'user' => '',
	'pass' => '',
	'name' => 'reborn_production'
);

$Database['test'] = array(
	'type' => 'mysql',
	'host' => '127.0.0.1',
	'port' => 3306,
	'user' => '',
	'pass' => '',
	'name' => 'reborn_test'
);
