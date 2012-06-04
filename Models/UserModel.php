<?php
/**
 * Reborn User Model
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class UserModel extends \CLx\Core\Model {
	public function __construct() {
		parent::__construct();
		// Load Extend Library
		\CLx\Core\Loader::Library('StatusCode');
	}
	
	// Create User
	public function createUser($username, $password, $email) {
		if(NULL == $username) {
			StatusCode::SetStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			StatusCode::SetStatus(2002);
			return FALSE;
		}
		elseif(NULL == $email) {
			StatusCode::SetStatus(2003);
			return FALSE;
		}
		
		// Regular express
		$regeUser = '/^\w{4,16}$/';
		$regeEmail = '/^([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]+)$/';
		
		if(!preg_match($regeUser, $username) || !preg_match($regeEmail, $email)) {
			StatusCode::SetStatus(3002);
			return FALSE;
		}
		
		if($this->isUserExist($username)) {
			StatusCode::SetStatus(3003);
			return FALSE;
		}
		
		$sql = 'INSERT INTO `accounts` SET `username`=:un, `password`=:pw, `email`=:em';
		$params = array(':un' => $username, ':pw' => $password, ':em' => $email);
		if($this->DB->Query($sql, $params)->InsertId())
			return TRUE;
		else
			return FALSE;
	}

	// Update User Password
	public function updateUserPassword($username, $oldpassword, $newpassword) {
		if(NULL == $username) {
			StatusCode::SetStatus(2001);
			return FALSE;
		}
		elseif(NULL == $oldpassword) {
			StatusCode::SetStatus(2002);
			return FALSE;
		}
		elseif(NULL == $newpassword) {
			StatusCode::SetStatus(2002);
			return FALSE;
		}

		// Check update success
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un AND `password`=:opw';
		$params = array(':un' => $username, ':opw' => $oldpassword);
		if(0 == count($this->DB->Query($sql, $params)->AsArray())) {
			StatusCode::SetStatus(3002);
			return FALSE;
		}

		// Update password
		$sql = 'UPDATE `accounts` SET `password`=:npw WHERE `username`=:un AND `password`=:opw';
		$params = array(
			':npw' => $newpassword,
			':un' => $username,
			':opw' => $oldpassword
		);
		$this->DB->Query($sql, $params);
		
		// Check update success
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un AND `password`=:npw';
		$params = array(':un' => $username, ':npw' => $newpassword);
		if(0 == count($this->DB->Query($sql, $params)->AsArray())) {
			StatusCode::SetStatus(3002);
			return FALSE;
		}

		return TRUE;
	}
	
	private function isUserExist($username) {
		if(NULL != $username) {
			$sql = 'SELECT username FROM `accounts` WHERE `username`=:un';
			$params = array(':un' => $username);
			return 0 != count($this->DB->Query($sql, $params)->AsArray()) ? TRUE : FALSE;
		}
		return FALSE;
	}
	
	// Delete User
	public function deleteUser($username, $password) {
		if(NULL == $username) {
			StatusCode::SetStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			StatusCode::SetStatus(2002);
			return FALSE;
		}

		if(!$this->isUserExist($username)) {
			StatusCode::SetStatus(3001);
			return FALSE;
		}
		
		$sql = 'DELETE FROM `accounts` WHERE `username`=:un AND `password`=:pw';
		$params = array(':un' => $username, ':pw' => $password);
		$this->DB->Query($sql, $params);
		if(!$this->isUserExist($username))
			return TRUE;
	}

	public function getUserUseUsername($username) {
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un';
		$params = array(':un' => $username);
		return $this->DB->Query($sql, $params)->AsArray();
	}
}
