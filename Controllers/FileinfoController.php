<?php
/**
 * RNFileSystem File Information Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class FilelistController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
		
		// Load Config
		$this->file_config = \CLx\Core\Loader::config('Config', 'file');

		// Load Library
		\CLx\Core\Loader::library('StatusCode');
		\CLx\Core\Loader::library('VirFL');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		$this->file_model = \CLx\Core\Loader::model('File');
	}
	
	/**
	 * Get File or List
	 */
	public function read($segments) {
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		$token = isset($headers['Access-Token']) ? $headers['Access-Token'] : NULL;
		$version = isset($params['version']) ? $params['version'] : 0;

		if($username = $this->auth_model->updateToken($token)) {
			// Database Disconnect
			\CLx\Library\Database::disconnect();
			
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize VirFL
			VirFL::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);

			// Check file is exists
			if(!VirFL::isExists($path))
				StatusCode::setStatus(3004);
			
			// Load file or list
			if(!StatusCode::isError()) {
				if(VirFL::isDir($path))
					CLx\Core\Response::toJSON(VirFL::index($path));
				else
					VirFL::read($path, NULL, $version);
			}
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
