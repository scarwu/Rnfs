<?php
/**
 * Reborn Status Code Library
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class StatusCode {
	/**--------------------------------------------------
	 * Error Code
	 * --------------------------------------------------
	 */
	private static $_error_code = array(
		// Normal
		1000 => array(200, 'OK'),
		
		// Something was missing
		2000 => array(401, 'Token is missing'),
		2001 => array(400, 'Username is missing'),
		2002 => array(400, 'Password is missing'),
		2003 => array(400, 'E-mail is missing'),
		
		// Something was error
		3000 => array(401, 'Token is invaild'),
		3001 => array(404, 'User isn\'t found'),
		3002 => array(403, 'Username or Password Error'),
		3003 => array(403, 'User is existence'),
		3004 => array(404, 'File or dir is not found'),
		3005 => array(400, 'Upload failed'),
		3006 => array(403, 'File operations Error'),
		3007 => array(403, 'File or dir is existence'),
		
		// Other Error
		4000 => array(403, 'Capacity is full'),
		4001 => array(403, 'File is over upload limit')
	);
	private static $_is_error = FALSE;
	private static $_code = 1000;
	
	private function __construct() {}
	
	/**--------------------------------------------------
	 * Set Status Code
	 * --------------------------------------------------
	 */
	public static function setStatus($code) {
		if(FALSE == self::$_is_error && 1000 != $code && isset(self::$_error_code[$code])) {
			self::$_is_error = TRUE;
			self::$_code = $code;
			\CLx\Core\Response::setCode(self::$_error_code[$code][0]);
		}
	}
	
	/**--------------------------------------------------
	 * Get Status Code
	 * --------------------------------------------------
	 */
	public static function getStatus() {
		if(1000 == self::$_code)
			\CLx\Core\Response::setCode(200);
		
		return array(self::$_code => self::$_error_code[self::$_code][1]);
	}
	
	/**--------------------------------------------------
	 * Get Status List
	 * --------------------------------------------------
	 */
	public static function getStatusList() {
		$list = array();
		foreach((array)self::$_error_code as $key => $value)
			$list[$key] = array(
				'status' => $value[0],
				'message' => $value[1]
			);
		
		return $list;
	}
	
	/**--------------------------------------------------
	 * Is Error
	 * --------------------------------------------------
	 */
	public static function isError() {
		return self::$_is_error;
	}
}
