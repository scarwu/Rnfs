<?php
/**
 * User Model
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class UserModel extends \CLx\Core\Model {
	public function __construct() {
		parent::__construct();
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
	}
	
	// Create User
	public function createUser($username, $password, $email) {
		if(NULL == $username) {
			StatusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			StatusCode::setStatus(2002);
			return FALSE;
		}
		elseif(NULL == $email) {
			StatusCode::setStatus(2003);
			return FALSE;
		}
		
		// Regular express
		$regex_username = '/^\w{4,16}$/';
		$regex_email = '/^([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]+)$/';
		
		if(!preg_match($regex_username, $username) || !preg_match($regex_email, $email)) {
			StatusCode::setStatus(3002);
			return FALSE;
		}
		
		if($this->isUserExist($username)) {
			StatusCode::setStatus(3003);
			return FALSE;
		}
		
		$sql = 'INSERT INTO accounts SET username=:un, password=:pw, email=:em';
		$params = array(':un' => $username, ':pw' => $password, ':em' => $email);
		if($this->_db->query($sql, $params)->insertId())
			return TRUE;
		else
			return FALSE;
	}

	// Update User Password
	public function updateUserPassword($username, $old_password, $new_password) {
		if(NULL == $username) {
			StatusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $old_password) {
			StatusCode::setStatus(2002);
			return FALSE;
		}
		elseif(NULL == $new_password) {
			StatusCode::setStatus(2002);
			return FALSE;
		}

		// Update password
		$sql = 'UPDATE accounts SET password=:npw WHERE username=:un AND password=:opw';
		$params = array(
			':npw' => $new_password,
			':un' => $username,
			':opw' => $old_password
		);
		$this->_db->query($sql, $params);
		
		// Check update success
		if(0 == count($this->_db->query($sql, $params)->count())) {
			StatusCode::setStatus(3002);
			return FALSE;
		}

		return TRUE;
	}
	
	private function isUserExist($username) {
		if(NULL != $username) {
			$sql = 'SELECT username FROM accounts WHERE username=:un';
			$params = array(':un' => $username);
			return 0 != count($this->_db->query($sql, $params)->asArray()) ? TRUE : FALSE;
		}
		return FALSE;
	}
	
	// Delete User
	public function deleteUser($username, $password) {
		if(NULL == $username) {
			StatusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			StatusCode::setStatus(2002);
			return FALSE;
		}

		if(!$this->isUserExist($username)) {
			StatusCode::setStatus(3001);
			return FALSE;
		}
		
		$sql = 'DELETE FROM accounts WHERE username=:un AND password=:pw';
		$params = array(':un' => $username, ':pw' => $password);
		$this->_db->query($sql, $params);
		if(!$this->isUserExist($username)) {
			$sql = 'DELETE FROM tokenlist WHERE username=:un';
			$params = array(':un' => $username);
			$this->_db->query($sql, $params);
			return TRUE;
		}
	}

	public function getUserUseUsername($username) {
		$sql = 'SELECT username,email FROM accounts WHERE username=:un';
		$params = array(':un' => $username);
		return $this->_db->query($sql, $params)->asArray();
	}
}
