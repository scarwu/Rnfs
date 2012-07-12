<?php
/**
 * RNFileSystem User Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
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
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		// Get headers & segments detail
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Load VirFL and Initialize
			\CLx\Core\Loader::Library('VirFL');
			VirFL::init(FILE_LOCATE, $this->file_config['revert']);
			
			// Load User Information
			$result = $this->user_model->getUserUseUsername($username);
			
			// Response Result
			\CLx\Core\Response::toJSON(array(
				'username' => $result[0]['username'],
				'email' => $result[0]['email'],
				'upload_limit' => $this->file_config['upload_limit'],
				'capacity' => $this->file_config['capacity'],
				'used' => VirFL::getUsed()
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
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		// Get headers & segments detail
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);
		elseif($username != $this->auth_model->updateToken($token))
			StatusCode::setStatus(3000);
		
		if(!StatusCode::isError()) {
			$old_password = isset($params['old_password']) ? hash('md5', $params['old_password']) : NULL;
			$new_password = isset($params['new_password']) ? hash('md5', $params['new_password']) : NULL;
			
			if($this->user_model->updateUserPassword($username, $old_password, $new_password)) {
				// Delete Token
				$this->auth_model->deleteDBTokenByTime($username, time()+$this->auth_config['timeout']);
			}
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * 
	 */
	public function delete($segments) {
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		// Get headers & segments detail
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		
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
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
