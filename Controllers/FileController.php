<?php
/**
 * RNFileSystem Files Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
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
	
	/**
	 * Create File
	 */
	public function create($segments) {
		$headers = \CLx\Core\Request::headers();
		$files = \CLx\Core\Request::files();
		
		$token = isset($headers['Access-Token']) ? $headers['Access-Token'] : NULL;
		
		if($username = $this->auth_model->updateToken($token)) {
			// Database Disconnect
			\CLx\Library\Database::disconnect();
			
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

				if(!StatusCode::isError()) {
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'create',
							'type' => 'file',
							'path' => $path,
							'hash' => hash_file('md5', $files['tmp_name']),
							'size' => $files['size'],
							'version' => 0
						)
					));
				}
				
				// Unlink temp file
				if(file_exists($files['tmp_name']))
					unlink($files['tmp_name']);
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
	public function update($segments) {
		$headers = \CLx\Core\Request::headers();
		$files = \CLx\Core\Request::files();
		
		$token = isset($headers['Access-Token']) ? $headers['Access-Token'] : NULL;
		
		if($username = $this->auth_model->updateToken($token)) {
			// Database Disconnect
			\CLx\Library\Database::disconnect();
			
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize VirFL
			VirFL::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);
			
			// Check Old Path
			if(!VirFL::isExists($path))
				StatusCode::setStatus(3004);
			
			if(NULL !== $files && !StatusCode::isError()) {
				// File Upload Handler
				if(0 !== $files['error'])
					StatusCode::setStatus(3005);
				
				// Check capacity used
				if($files['size'] + VirFL::getUsed() > $this->file_config['capacity'])
					StatusCode::setStatus(4000);
				
				if(!StatusCode::isError()) {
					// VirFL Create File
					if(!VirFL::update($path, $files['tmp_name']))
						StatusCode::setStatus(3006);
				}
				
				if(!StatusCode::isError()) {
					$info = VirFL::index($path);
					
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'update',
							'type' => 'file',
							'path' => $path,
							'hash' => $info['hash'],
							'size' => $info['size'],
							'version' => $info['version']
						)
					));
				}
				
				// Unlink temp file
				if(file_exists($files['tmp_name']))
					unlink($files['tmp_name']);
			}
			elseif(!StatusCode::isError()) {
				$params = \CLx\Core\Request::params();
				
				$new_path = isset($params['path']) ? $params['path'] : NULL;
				$new_path = $this->file_model->parsePath(explode('/', trim($new_path, '/')));
				
				// Check Old Path and New Path
				if(NULL !== $new_path && VirFL::isExists($new_path))
					StatusCode::setStatus(3007);
				
				if(!StatusCode::isError()) {
					// Move path
					if(!VirFL::move($path, $new_path))
						StatusCode::setStatus(3006);
				}
				
				if(!StatusCode::isError()) {
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'rename',
							'type' => VirFL::type($new_path),
							'path' => $path,
							'new_path' => $new_path
						)
					));
				}
			}
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Delete File or Directory
	 */
	public function delete($segments) {
		$headers = \CLx\Core\Request::headers();
		
		$token = isset($headers['Access-Token']) ? $headers['Access-Token'] : NULL;

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
