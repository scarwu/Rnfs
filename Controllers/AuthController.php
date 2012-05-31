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
		// Load Extend Library
		\CLx\Core\Loader::Library('StatusCode');
		// Load Model
		$this->AuthModel = \CLx\Core\Loader::Model('Auth');
	}

	public function read() {
		
	}

	/**
	 * Create Token
	 */
	public function create() {
		$params = \CLx\Core\Request::params();
		
		// Get params detail
		$username = isset($params['username']) ? $params['username'] : NULL;
		$password = isset($params['password']) ? $params['password'] : NULL;
		
		if($token = $this->authModel->genToken($username, $password))
			$this->view->json(array('status' => statusCode::getStatus(), 'token' => $token));
		else
			$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Update Token Alive Time
	 */
	public function update() {
		$params = request::params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->authModel->updateToken($token);
		$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Delete Token
	 */
	public function delete() {
		$params = request::params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->authModel->deleteToken($token);
		$this->view->json(array('status' => statusCode::getStatus()));
	}
}
