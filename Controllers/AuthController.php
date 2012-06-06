<?php
/**
 * Reborn Authentication Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
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
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus(), 'token' => $token));
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Update Token Alive Time
	 */
	public function update() {
		$params = \CLx\Core\Request::params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->auth_model->updateToken($token);
		\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Delete Token
	 */
	public function delete() {
		$params = \CLx\Core\Request::params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->auth_model->deleteToken($token);
		\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
