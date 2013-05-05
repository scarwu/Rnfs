<?php
/**
 * DFS Library
 * 
 * @package		Parliament
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class Parliament {

	/**
	 * @var string
	 */
	private static $_ip_address = '127.0.0.1';

	/**
	 * @var int
	 */
	private static $_port = 6000;

	/**
	 * Set Server Host
	 */
	public static function setHost($ip, $port = NULL) {
		self::$_ip_address = $ip;
		self::$_port = $port != NULL ? $port : self::$_port;
	}

	/**
	 * Read DFS File
	 */
	public static function read($unique_id) {
		$json = json_encode(array(
			'action' => 'read',
			'unique_id' => $unique_id
		));

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connection = socket_connect($socket, self::$_ip_address, self::$_port);

		if(!socket_write($socket, $json))
			return FALSE;

		while($buffer = socket_read($socket, 1024)) {
			echo $buffer;
		}

		socket_close($socket);
		return TRUE;
	}

	/**
	 * Create DFS File
	 */
	public static function create($unique_id, $source) {
		$json = json_encode(array(
			'action' => 'create',
			'unique_id' => $unique_id,
			'source' => $source
		));

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connection = socket_connect($socket, self::$_ip_address, self::$_port);

		if(!socket_write($socket, $json))
			return FALSE;

		socket_close($socket);
		return TRUE;
	}

	/**
	 * Delete DFS File
	 */
	public static function delete($unique_id) {
		$json = json_encode(array(
			'action' => 'delete',
			'unique_id' => $unique_id
		));

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connection = socket_connect($socket, self::$_ip_address, self::$_port);

		if(!socket_write($socket, $json))
			return FALSE;

		socket_close($socket);
		return TRUE;
	}

	/**
	 * Get DFS File is Exists
	 */
	public static function isExists($unique_id) {
		$json = json_encode(array(
			'action' => 'exists',
			'unique_id' => $unique_id
		));

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$connection = socket_connect($socket, self::$_ip_address, self::$_port);

		if(!socket_write($socket, $json))
			return FALSE;

		$response = NULL;
		while($buffer = socket_read($socket, 1024)) {
			$response .= $buffer;
		}
		$response = json_decode($response, TRUE);

		socket_close($socket);
		return $response['exists'];
	}
}