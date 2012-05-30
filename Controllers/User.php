<?php
/**
 * Reborn User Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class User extends \CLx\Core\Controller {
	public function __construct() {
		parent::__construct();
		// Load Config
		$this->authConfig = $this->load->config('auth');
		$this->fileConfig = $this->load->config('file');
		// Load Library
		$this->load->sysLib('db');
		$this->load->sysLib('event');
		// Load Extend Library
		$this->load->extLib('statusCode');
		// Load Model
		$this->load->model('authModel');
		$this->load->model('fileModel');
		$this->load->model('userModel');
	}

	/**
	 * Create User Account
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function create() {
		$segments = request::segments();
		$params = request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$password = isset($params['password']) ? hash('md5', $params['password']) : NULL;
		$email = isset($params['email']) ? $params['email'] : NULL;

		if($this->userModel->createUser($username, $password, $email)) {
			// Trigger event
			event::trigger('userCreate');
		}
		
		$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Read User Information
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function read() {
		$segments = request::segments();
		$params = request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			statusCode::setStatus(2001);
		elseif($username != $this->authModel->updateToken($token))
			statusCode::setStatus(3000);
		
		if(!statusCode::isError()) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$result = $this->userModel->getUserUseUsername($username);
			$this->view->json(array(
				'status' => statusCode::getStatus(),
				'email' => $result[0]['email'],
				'uploadLimit' => $this->fileConfig['size'],
				'capacity' => $this->fileConfig['capacity'],
				'used' => $this->fileModel->getAllFileSize('/')
			));
		}
		else
			$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Update User Informatiom
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function update() {
		$segments = request::segments();
		$params = request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			statusCode::setStatus(2001);
		elseif($username != $this->authModel->updateToken($token))
			statusCode::setStatus(3000);
		
		if(!statusCode::isError()) {
			$newpassword = isset($params['newpassword']) ? hash('md5', $params['newpassword']) : NULL;
			$oldpassword = isset($params['oldpassword']) ? hash('md5', $params['oldpassword']) : NULL;
			
			if($this->userModel->updateUserPassword($username, $oldpassword, $newpassword)) {
				// Delete Token
				$this->authModel->deleteDBTokenByTime($username, time()+$this->authConfig['timeout']);
			}
		}

		$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Delete User Account
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function delete() {
		$segments = request::segments();
		$params = request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			statusCode::setStatus(2001);
		elseif($username != $this->authModel->updateToken($token))
			statusCode::setStatus(3000);
		
		if(!statusCode::isError()) {
			$password = isset($params['password']) ? hash('md5', $params['password']) : NULL;
			
			if($this->userModel->deleteUser($username, $password)) {
				// Trigger event
				event::trigger('userDelete');
			}
		}
		
		$this->view->json(array('status' => statusCode::getStatus()));
	}
}
