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

class UserController extends \CLx\Core\Controller {
	
	public function __construct() {
		parent::__construct();
		// Load Config
		$this->auth_config = \CLx\Core\Loader::Config('Config', 'auth');
		$this->file_config = \CLx\Core\Loader::Config('Config', 'file');
		// Load Library
		// $this->load->sysLib('db');
		// $this->load->sysLib('event');
		// Load Extend Library
		\CLx\Core\Loader::Library('StatusCode');
		// Load Model
		$this->auth_model = \CLx\Core\Loader::Model('Auth');
		$this->user_model = \CLx\Core\Loader::Model('User');
	}

	/**
	 * Create User Account
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function create($segments) {
		$params = \CLx\Core\Request::Params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$password = isset($params['password']) ? hash('md5', $params['password']) : NULL;
		$email = isset($params['email']) ? $params['email'] : NULL;

		if($this->user_model->createUser($username, $password, $email)) {
			// Trigger event
			\CLx\Core\Event::trigger('userCreate');
		}
		
		\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Read User Information
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function read($segments) {
		$params = \CLx\Core\Request::Params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$result = $this->user_model->getUserUseUsername($username);
			\CLx\Core\Response::toJSON(array(
				'status' => StatusCode::getStatus(),
				'email' => $result[0]['email'],
				'uploadLimit' => $this->file_config['size'],
				'capacity' => $this->file_config['capacity'] //,
				// 'used' => $this->fileModel->getAllFileSize('/')
			));
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Update User Informatiom
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function update($segments) {
		$params = \CLx\Core\Request::Params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			$newpassword = isset($params['newpassword']) ? hash('md5', $params['newpassword']) : NULL;
			$oldpassword = isset($params['oldpassword']) ? hash('md5', $params['oldpassword']) : NULL;
			
			if($this->user_model->updateUserPassword($username, $oldpassword, $newpassword)) {
				// Delete Token
				$this->auth_model->deleteDBTokenByTime($username, time()+$this->auth_config['timeout']);
			}
		}

		\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Delete User Account
	 * API: /users/{username}
	 * Input: 
	 * Output:
	 */
	public function delete($segments) {
		$params = \CLx\Core\Request::Params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			$password = isset($params['password']) ? hash('md5', $params['password']) : NULL;
			
			if($this->user_model->deleteUser($username, $password)) {
				// Trigger event
				event::trigger('userDelete');
			}
		}
		
		\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
