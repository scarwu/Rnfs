<?php
/**
 * RNFileSystem Events Config
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$Event['user_create'] = array();

$Event['user_delete'] = array();

$Event['file_change'] = array(
	// Send sync data
	function($callback) {
		// Load Config
		$sync_config = \CLx\Core\Loader::config('Config', 'sync');
		
		// Create Database Connect
		if($_database_config = \CLx\Core\Loader::config('Database', CLX_MODE)) {
			\CLx\Library\Database::setDB($_database_config);
			$db = \CLx\Library\Database::connect();
		}
		else
			throw new Exception('Config/Database.php is not exists.');

		if(!file_exists($sync_config['locate'] . $callback['user']))
			@mkdir($sync_config['locate'] . $callback['user'], 0755, TRUE);
		
		$sql = 'SELECT * FROM `tokenlist` WHERE `username`=:un';
		$params = array(':un' => $callback['user']);
		$result = $db->query($sql, $params)->asArray();
		
		foreach((array)$result as $row)
			if($row['token'] != $callback['token']) {
				$sockpath = $sync_config['locate'] . $callback['user'] . '/' . $row['token'];
				// $fp = fopen($sockpath, 'w+');
				$fp = fopen($sockpath, 'a');
				fwrite($fp, json_encode($callback['send']) . ',');
				fclose($fp);
			}
	}
);
