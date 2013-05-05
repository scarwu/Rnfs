<?php
/**
 * Files Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class FileController extends \CLx\Core\Controller {

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
		$version = isset($params['version']) ? $params['version'] : NULL;
		
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
			if(!StatusCode::isError()) {
				if(VirDFS::isDir($path))
					CLx\Core\Response::toJSON(VirDFS::index($path));
				else
					VirDFS::read($path, NULL, $version);
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
			if(VirDFS::isExists($path))
				StatusCode::setStatus(3007);
				
			if(NULL !== $files && !StatusCode::isError()) {
				// File Upload Handler
				if(0 !== $files['error'])
					StatusCode::setStatus(3005);
				
				// Check capacity used
				if($files['size'] + VirDFS::getUsed() > $this->file_config['capacity'])
					StatusCode::setStatus(4000);
				
				if(!StatusCode::isError()) {
					// VirDFS Create File
					if(!VirDFS::create($path, $files['tmp_name']))
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
				if(!VirDFS::create($path))
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
			
			// Check Old Path
			if(!VirDFS::isExists($path))
				StatusCode::setStatus(3004);
			
			if(NULL !== $files && !StatusCode::isError()) {
				// File Upload Handler
				if(0 !== $files['error'])
					StatusCode::setStatus(3005);
				
				// Check capacity used
				if($files['size'] + VirDFS::getUsed() > $this->file_config['capacity'])
					StatusCode::setStatus(4000);
				
				if(!StatusCode::isError()) {
					// VirDFS Create File
					if(!VirDFS::update($path, $files['tmp_name']))
						StatusCode::setStatus(3006);
				}
				
				if(!StatusCode::isError()) {
					$info = VirDFS::index($path);
					
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
				$new_path = $this->_parsePath(explode('/', trim($new_path, '/')));
				
				// Check Old Path and New Path
				if(NULL !== $new_path && VirDFS::isExists($new_path))
					StatusCode::setStatus(3007);
				
				if(!StatusCode::isError()) {
					// Move path
					if(!VirDFS::move($path, $new_path))
						StatusCode::setStatus(3006);
				}
				
				if(!StatusCode::isError()) {
					\CLx\Core\Event::trigger('file_change', array(
						'user' => $username,
						'token' => $token,
						'send' => array(
							'action' => 'rename',
							'type' => VirDFS::type($new_path),
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
			if(!StatusCode::isError()) {
				if(VirDFS::isDir($path)) {
					if(VirDFS::delete($path))
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
					if(VirDFS::delete($path))
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
