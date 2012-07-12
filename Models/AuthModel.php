<?php
/**
 * RNFileSystem Authentication Model
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class AuthModel extends \CLx\Core\Model {
	private $timeout;
	
	public function __construct() {
		parent::__construct();
		
		// Load Config
		$this->auth_config = \CLx\Core\Loader::config('Config', 'auth');
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
	}

	public function test() {
		$sql = 'SELECT * FROM accounts';
		return $this->_db->query($sql)->asArray();
	}

	// Token Generator
	public function genToken($username, $password) {
		if(NULL == $username) {
			StatusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			StatusCode::setStatus(2002);
			return FALSE;
		}

		// Check Username and Password is Valid
		if(!$this->loginByUsernameAndPassword($username, $password)) {
			StatusCode::setStatus(3002);
			return FALSE;
		}
		
		$time = time();
		
		// Delete timeout token
		$this->deleteDBTokenByTime($username, $time-$this->auth_config['timeout']);
		
		while(1) {
			$token = hash('sha256', rand().$time);
			if(!$this->loginByToken($token)) {
				$this->createDBToken($token, $username, $time);
				break;
			}
		}

		return $token;
	}
	
	// Update Token
	public function updateToken($token) {
		if(NULL == $token) {
			StatusCode::setStatus(2000);
			return FALSE;
		}
		
		$time = time();
		if(!($result = $this->loginByToken($token, $time-$this->auth_config['timeout']))) {
			StatusCode::setStatus(3000);
			return FALSE;
		}
		
		$this->updateDBTokenTimeByToken($token, $time);
		return $result[0]['username'];
	}
	
	// Delete Token
	public function deleteToken($token) {
		if(NULL == $token) {
			StatusCode::setStatus(2000);
			return FALSE;
		}

		if($this->loginByToken($token)) {
			$this->deleteDBTokenByToken($token);
			return TRUE;
		}
		else {
			StatusCode::setStatus(3000);
			return FALSE;
		}
	}
	
	/**
	 * Data Access Layer
	 */
	// Login By username and password
	private function loginByUsernameAndPassword($username, $password) {
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un AND `password`=:pw';
		$params = array(':un' => $username, ':pw' => hash('md5', $password));
		return 1 == count($this->_db->query($sql, $params)->asArray()) ? TRUE : FALSE;
	}
	
	// Login By token
	private function loginByToken($token, $time = NULL) {
		if(NULL == $time) {
			$sql = 'SELECT * FROM `tokenlist` WHERE `token`=:tk';
			$params = array(':tk' => $token);
		}
		else {
			$sql = 'SELECT * FROM `tokenlist` WHERE `token`=:tk AND `timestamp`>=:ti';
			$params = array(':tk' => $token, ':ti' => $time);
		}
		
		$result = $this->_db->query($sql, $params)->asArray();
		return 0 != count($result) ? $result : FALSE;
	}
		
	// Update Token Time by Time
	private function updateDBTokenTimeByToken($token, $time) {
		$sql = 'UPDATE `tokenlist` SET `timestamp`=:ti WHERE `token`=:tk';
		$params = array('ti' => $time, ':tk' => $token);
		$this->_db->query($sql, $params);
	}
	
	// Delete Token By Token
	private function deleteDBTokenByToken($token) {
		$sql = 'DELETE FROM `tokenlist` WHERE `token`=:tk';
		$params = array(':tk' => $token);
		$this->_db->query($sql, $params);
	}
	
	// Delete Token By Time
	public function deleteDBTokenByTime($username, $time) {
		$sql = 'DELETE FROM `tokenlist` WHERE `username`=:un AND `timestamp`<:ti';
		$params = array(':un' => $username, ':ti' => $time);
		$this->_db->query($sql, $params);
	}
	
	// Create Token
	private function createDBToken($token, $username, $time) {
		$sql = 'INSERT INTO `tokenlist` SET `token`=:tk, `username`=:un, `timestamp`=:ti';
		$params = array(':tk' => $token, ':un' => $username, ':ti' => $time);
		$this->_db->query($sql, $params);
	}
}
