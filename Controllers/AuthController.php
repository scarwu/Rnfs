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

	/**
	 * Create Token
	 */
	public function create() {
		$params = \CLx\Core\Request::Params();
		
		// Get params detail
		$username = isset($params['username']) ? $params['username'] : NULL;
		$password = isset($params['password']) ? $params['password'] : NULL;
		
		if($token = $this->AuthModel->genToken($username, $password))
			\CLx\Core\Response::ToJSON(array('status' => StatusCode::GetStatus(), 'token' => $token));
		else
			\CLx\Core\Response::ToJSON(array('status' => StatusCode::GetStatus()));
	}
	
	/**
	 * Update Token Alive Time
	 */
	public function update() {
		$params = \CLx\Core\Request::Params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->AuthModel->updateToken($token);
		\CLx\Core\Response::ToJSON(array('status' => StatusCode::GetStatus()));
	}
	
	/**
	 * Delete Token
	 */
	public function delete() {
		$params = \CLx\Core\Request::Params();
		
		// Get params detail
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		$this->AuthModel->deleteToken($token);
		\CLx\Core\Response::ToJSON(array('status' => StatusCode::GetStatus()));
	}
}
