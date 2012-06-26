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
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		
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
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		
		$this->auth_model->deleteToken($token);
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
