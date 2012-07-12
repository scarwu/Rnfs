<?php
/**
 * Reborn Files Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class FileController extends \CLx\Core\Controller {

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
		
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		$version = isset($params['version']) ? $params['version'] : 0;

		if($username = $this->auth_model->updateToken($token)) {
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
	
	/**
	 * Create File
	 */
	public function create($segments) {
		$headers = \CLx\Core\Request::headers();
		$files = \CLx\Core\Request::files();
		
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;
		
		if($username = $this->auth_model->updateToken($token)) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize VirFL
			VirFL::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);
			
			// Check file is exists
			if(VirFL::isExists($path))
				StatusCode::setStatus(3007);
				
			if(NULL !== $files && !StatusCode::isError()) {
				// File Upload Handler
				if(0 !== $files['error'])
					StatusCode::setStatus(3005);
				
				// Check capacity used
				if($files['size'] + VirFL::getUsed() > $this->file_config['capacity'])
					StatusCode::setStatus(4000);
				
				if(!StatusCode::isError()) {
					// VirFL Create File
					if(!VirFL::create($path, $files['tmp_name']))
						StatusCode::setStatus(3006);
				}
				
				// Unlink temp file
				unlink($files['tmp_name']);
				
				if(!StatusCode::isError()) {
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'create',
							'type' => 'file',
							'path' => $path
						)
					));
				}
			}
			elseif(!StatusCode::isError()) {
				// Create New Dir
				if(!VirFL::create($path))
					StatusCode::setStatus(3006);
				
				if(!StatusCode::isError()) {
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'create',
							'type' => 'dir',
							'path' => $path
						)
					));
				}
			}
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Update File
	 */
	// public function update($segments) {
		// $params = \CLx\Core\Request::params();
		// $files = \CLx\Core\Request::files();
// 		
		// $token = isset($params['token']) ? $params['token'] : NULL;
// 
		// if($username = $this->auth_model->updateToken($token)) {
			// define('FILE_LOCATE', $this->file_config['locate'] . $username);
//
// 			// Initialize VirFL
			// VirFL::init(FILE_LOCATE);
// 			
			// $path = $this->file_model->parsePath($segments);
			// $current_path = FILE_LOCATE . $path;
// 			
			// $newpath = isset($params['newpath']) ? $params['newpath'] : NULL;
			// $newpath = explode('/', trim($newpath, '/'));
			// $newpath = $this->file_model->parsePath($newpath);
// 			
			// if(file_exists($current_path) && $path != '') {
				// if($files != NULL) {
					// if(0 != $files['error'] || md5_file($files['tmp_name']) == md5_file($current_path))
						// StatusCode::setStatus(3005);
// 					
					// if(($files['size'] - filesize($current_path)) + $this->file_model->getAllFileSize('/') > $this->file_config['capacity'])
						// StatusCode::setStatus(4000);
// 					
					// if(!StatusCode::isError()) {
						// $dirpath = $segments;
						// array_pop($dirpath);
						// $dirpath = FILE_LOCATE . $this->file_model->parsePath($dirpath);
// 						
						// if(!file_exists($dirpath))
							// mkdir($dirpath, 0755, TRUE);
// 						
						// if(!unlink($current_path) || !copy($files['tmp_name'], $current_path))
							// StatusCode::setStatus(3006);
					// }
// 					
					// if(!StatusCode::isError()) {
						// \CLx\Core\Event::trigger('file_change', array(
							// 'user' => $username,
							// 'token' => $token,
							// 'send' => array(
								// 'action' => 'update',
								// 'type' => 'file',
								// 'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path),
								// 'hash' => md5_file($current_path)
							// )
						// ));
					// }
				// }
				// else if($newpath !== NULL && !file_exists(FILE_LOCATE . $newpath) ) {
					// if(!rename($current_path, FILE_LOCATE . $newpath))
						// StatusCode::setStatus(3006);
// 					
					// if(!StatusCode::isError()) {
						// \CLx\Core\Event::trigger('file_change', array(
							// 'user' => $username,
							// 'token' => $token,
							// 'send' => array(
								// 'action' => 'rename',
								// 'type' => is_dir(FILE_LOCATE . $newpath) ? 'dir' : 'file',
								// 'oldpath' => str_replace(DIRECTORY_SEPARATOR, '/', $path),
								// 'path' => str_replace(DIRECTORY_SEPARATOR, '/', $newpath)
							// )
						// ));
					// }
				// }
			// }
			// else
				// StatusCode::setStatus(3007);
		// }
// 
		// \CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	// }
	
	/**
	 * Delete File or Dir
	 */
	public function delete($segments) {
		$headers = \CLx\Core\Request::headers();
		
		$token = isset($headers['Reborn-Token']) ? $headers['Reborn-Token'] : NULL;

		if($username = $this->auth_model->updateToken($token)) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize VirFL
			VirFL::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);
			
			// Check file is exists
			if(!VirFL::isExists($path))
				StatusCode::setStatus(3004);
			
			// Load file or list
			if(!StatusCode::isError()) {
				if(VirFL::isDir($path)) {
					if(VirFL::delete($path))
						\CLx\Core\Event::trigger('file_change', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'delete',
								'type' => 'dir',
								'path' => $path
							)
						));
					else
						StatusCode::setStatus(3006);
				}
				else {
					if(VirFL::delete($path))
						\CLx\Core\Event::trigger('file_change', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'delete',
								'type' => 'file',
								'path' => $path
							)
						));
					else
						StatusCode::setStatus(3006);
				}
			}
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
