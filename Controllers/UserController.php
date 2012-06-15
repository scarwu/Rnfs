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
		$this->auth_config = \CLx\Core\Loader::config('Config', 'auth');
		$this->file_config = \CLx\Core\Loader::config('Config', 'file');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		$this->user_model = \CLx\Core\Loader::model('User');
		
		// Load Extend Library
		\CLx\Core\Loader::library('StatusCode');
	}

	/**
	 * 
	 */
	public function read($segments) {
		$params = \CLx\Core\Request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Load SimFS and Initialize
			\CLx\Core\Loader::Library('SimFS');
			SimFS::init(FILE_LOCATE, $this->file_config['revert']);
			
			// Load User Information
			$result = $this->user_model->getUserUseUsername($username);
			
			// Response Result
			\CLx\Core\Response::toJSON(array(
				'username' => $result[0]['username'],
				'email' => $result[0]['email'],
				'upload_limit' => $this->file_config['upload_limit'],
				'capacity' => $this->file_config['capacity'],
				'used' => SimFS::getUsed()
			));
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}

	/**
	 * 
	 */
	public function create($segments) {
		$params = \CLx\Core\Request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$password = isset($params['password']) ? hash('md5', $params['password']) : NULL;
		$email = isset($params['email']) ? $params['email'] : NULL;

		if($this->user_model->createUser($username, $password, $email)) {
			// Trigger event
			\CLx\Core\Event::trigger('user_create');
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * 
	 */
	public function update($segments) {
		$params = \CLx\Core\Request::params();
		
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
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * 
	 */
	public function delete($segments) {
		$params = \CLx\Core\Request::params();
		
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
				\CLx\Core\Event::trigger('user_delete');
			}
		}

		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
