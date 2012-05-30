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

class userModel extends \CLx\Core\Model {
	public function __construct() {
		parent::__construct();
		// Load Extend Library
		$this->load->extLib('statusCode');
	}
	
	// Create User
	public function createUser($username, $password, $email) {
		if(NULL == $username) {
			statusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			statusCode::setStatus(2002);
			return FALSE;
		}
		elseif(NULL == $email) {
			statusCode::setStatus(2003);
			return FALSE;
		}
		
		// Regular express
		$regeUser = '/^\w{4,16}$/';
		$regeEmail = '/^([\w-\.]+)@((?:[\w]+\.)+)([a-zA-Z]+)$/';
		
		if(!preg_match($regeUser, $username) || !preg_match($regeEmail, $email)) {
			statusCode::setStatus(3002);
			return FALSE;
		}
		
		if($this->isUserExist($username)) {
			statusCode::setStatus(3003);
			return FALSE;
		}
		
		$sql = 'INSERT INTO `accounts` SET `username`=:un, `password`=:pw, `email`=:em';
		$params = array(':un' => $username, ':pw' => $password, ':em' => $email);
		if($this->db->query($sql, $params)->insertId())
			return TRUE;
		else
			return FALSE;
	}

	// Update User Password
	public function updateUserPassword($username, $oldpassword, $newpassword) {
		if(NULL == $username) {
			statusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $oldpassword) {
			statusCode::setStatus(2002);
			return FALSE;
		}
		elseif(NULL == $newpassword) {
			statusCode::setStatus(2002);
			return FALSE;
		}

		// Check update success
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un AND `password`=:opw';
		$params = array(':un' => $username, ':opw' => $oldpassword);
		if(0 == count($this->db->query($sql, $params)->asAry())) {
			statusCode::setStatus(3002);
			return FALSE;
		}

		// Update password
		$sql = 'UPDATE `accounts` SET `password`=:npw WHERE `username`=:un AND `password`=:opw';
		$params = array(
			':npw' => $newpassword,
			':un' => $username,
			':opw' => $oldpassword
		);
		$this->db->query($sql, $params);
		
		// Check update success
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un AND `password`=:npw';
		$params = array(':un' => $username, ':npw' => $newpassword);
		if(0 == count($this->db->query($sql, $params)->asAry())) {
			statusCode::setStatus(3002);
			return FALSE;
		}

		return TRUE;
	}
	
	private function isUserExist($username) {
		if(NULL != $username) {
			$sql = 'SELECT username FROM `accounts` WHERE `username`=:un';
			$params = array(':un' => $username);
			return 0 != count($this->db->query($sql, $params)->asAry()) ? TRUE : FALSE;
		}
		return FALSE;
	}
	
	// Delete User
	public function deleteUser($username, $password) {
		if(NULL == $username) {
			statusCode::setStatus(2001);
			return FALSE;
		}
		elseif(NULL == $password) {
			statusCode::setStatus(2002);
			return FALSE;
		}

		if(!$this->isUserExist($username)) {
			statusCode::setStatus(3001);
			return FALSE;
		}
		
		$sql = 'DELETE FROM `accounts` WHERE `username`=:un AND `password`=:pw';
		$params = array(':un' => $username, ':pw' => $password);
		$this->db->query($sql, $params);
		if(!$this->isUserExist($username))
			return TRUE;
	}

	public function getUserUseUsername($username) {
		$sql = 'SELECT * FROM `accounts` WHERE `username`=:un';
		$params = array(':un' => $username);
		return $this->db->query($sql, $params)->asAry();
	}
}
