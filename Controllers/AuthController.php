<?php
/**
 * Authentication Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class AuthController extends \CLx\Core\Controller {
	
	public function __construct() {
		parent::__construct();
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
	}

	/**
	 * Create Token
	 */
	public function create() {
		$params = \CLx\Core\Request::params();
		
		// Get params detail
		$username = isset($params['username']) ? $params['username'] : NULL;
		$password = isset($params['password']) ? $params['password'] : NULL;
		
		if($token = $this->auth_model->genToken($username, $password))
			\CLx\Core\Response::toJSON(array('token' => $token));
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Update Token Alive Time
	 */
	public function update() {
		$headers = \CLx\Core\Request::headers();
		
		// Get headers detail
		$token = isset($headers['X-Rnfs-Token']) ? $headers['X-Rnfs-Token'] : NULL;
		
		$this->auth_model->updateToken($token);
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Delete Token
	 */
	public function delete() {
		$headers = \CLx\Core\Request::headers();
		
		// Get headers detail
		$token = isset($headers['X-Rnfs-Token']) ? $headers['X-Rnfs-Token'] : NULL;
		
		$this->auth_model->deleteToken($token);
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
