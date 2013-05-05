<?php
/**
 * File Information Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class FileinfoController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
		
		// Load Config
		$this->file_config = \CLx\Core\Loader::config('Config', 'file');
		$this->database_config = \CLx\Core\Loader::config('Database', CLX_MODE);

		// Load Library
		\CLx\Core\Loader::library('StatusCode');
		\CLx\Core\Loader::library('Parliament');
		\CLx\Core\Loader::library('VirDFS');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
	}
	
	/**
	 * Parse Path
	 * 
	 * @param	string $segments
	 * @return	string $path
	 */
	private function _parsePath($segments = NULL) {
		$blacklist = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
		$path = '/';
		foreach((array)$segments as $value)
			if($value != '.' || $value != '..') {
				$value = str_replace($blacklist, '', $value);
				$path .= $path == '/' ? $value : '/' . $value;
			}
		return $path;
	}

	/**
	 * Get File or List
	 */
	public function read($segments) {
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		$token = isset($headers['X-Rnfs-Token']) ? $headers['X-Rnfs-Token'] : NULL;
		
		if($username = $this->auth_model->updateToken($token)) {
			// Database Disconnect
			\CLx\Library\Database::disconnect();
			
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize VirDFS
			VirDFS::init(array(
				'username' => $username,
				'root' => FILE_LOCATE,
				'revert' => $this->file_config['revert'],
				'backup' => $this->file_config['backup'],
				'user' => $this->database_config['user'],
				'pass' => $this->database_config['pass'],
				'host' => $this->database_config['host'],
				'port' => $this->database_config['port'],
				'name' => $this->database_config['name']
			));
			
			$path = $this->_parsePath($segments);

			// Check file is exists
			if(!VirDFS::isExists($path))
				StatusCode::setStatus(3004);
			
			// Load file or list
			if(!StatusCode::isError())
				CLx\Core\Response::toJSON(VirDFS::info($path));
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
