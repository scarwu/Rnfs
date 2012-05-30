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

class statusCode {
	/**--------------------------------------------------
	 * Error Code
	 * --------------------------------------------------
	 */
	private static $errorCode = array(
		// Normal
		1000 => array(200, 'OK'),
		
		// Something was missing
		2000 => array(400, 'Token is missing'),
		2001 => array(400, 'Username is missing'),
		2002 => array(400, 'Password is missing'),
		2003 => array(400, 'E-mail is missing'),
		2004 => array(400, 'Path is missing'),
		2005 => array(400, 'Newpath is missing'),
		
		// Something was error
		3000 => array(401, 'Token is invaild'),
		3001 => array(404, 'User is\'t found'),
		3002 => array(403, 'Username or Password Error'),
		3003 => array(403, 'User is existence'),
		3004 => array(404, 'Path is not found'),
		3005 => array(400, 'Upload failed'),
		3006 => array(403, 'File operations Error'),
		3007 => array(403, 'File or dir is existence'),
		
		// Other Error
		4000 => array(403, 'Capacity is full'),
		4001 => array(403, 'File is over upload limit')
	);
	private static $isError = FALSE;
	private static $code = 1000;
	
	/**--------------------------------------------------
	 * Set Status Code
	 * --------------------------------------------------
	 */
	public static function setStatus($code) {
		if(FALSE == self::$isError && 1000 != $code && isset(self::$errorCode[$code])) {
			self::$isError = TRUE;
			self::$code = $code;
			response::sendHeader(self::$errorCode[$code][0]);
		}
	}
	
	/**--------------------------------------------------
	 * Get Status Code
	 * --------------------------------------------------
	 */
	public static function getStatus() {
		if(1000 == self::$code)
			response::sendHeader(200);
		
		return array(
			'http' => self::$errorCode[self::$code][0],
			'code' => self::$code,
			'msg' => self::$errorCode[self::$code][1]
		);
	}
	
	/**--------------------------------------------------
	 * Get Status List
	 * --------------------------------------------------
	 */
	public static function getStatusList() {
		$list = array();
		foreach((array)self::$errorCode as $key => $value)
			array_push($list, array(
				'code' => $key,
				'http' => $value[0],
				'msg' => $value[1]
			));
		
		return $list;
	}
	
	/**--------------------------------------------------
	 * Is Error
	 * --------------------------------------------------
	 */
	public static function isError() {
		return 1000 != self::$code;
	}
}
