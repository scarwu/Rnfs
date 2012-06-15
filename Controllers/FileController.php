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
		\CLx\Core\Loader::library('SimFS');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		$this->file_model = \CLx\Core\Loader::model('File');
	}
	
	/**
	 * Get File or List
	 */
	public function read($segments) {
		$params = \CLx\Core\Request::params();
		
		$token = isset($params['token']) ? $params['token'] : NULL;
		$version = isset($params['version']) ? $params['version'] : 0;

		if($username = $this->auth_model->updateToken($token)) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize SimFS
			SimFS::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);
			// $current_path = FILE_LOCATE . $path;
			
			print_r(SimFS::index());
			
			// SimFS::read($path, NULL, $version);
			
			// if(file_exists($current_path) && is_file($current_path)) {
				// $filerange = isset($params['filerange']) ? $params['filerange'] : NULL;
// 
				// $filesize = filesize($current_path);
				// $filename = $segments[count($segments) - 1];
// 				
				// if($filerange == NULL) {
					// header('Accept-Ranges: bytes');
					// header('Content-Length: '. $filesize); 
					// header('HTTP/1.1 200 OK'); 
					// header('Content-Type: ' . $this->file_model->getMimetype($current_path));
					// header('Content-Disposition: attachment; filename=' . $filename);
// 					
					// ob_end_flush();
					// readfile($current_path);
				// }
				// else {
					// $fp = fopen($current_path, 'rb');
// 					
					// header('Accept-Ranges: bytes');
					// header('Content-Length: '. ($filesize - $range)); 
					// header('HTTP/1.1 206 Partial Content'); 
					// header('Content-Type: ' . $this->file_model->getMimetype($current_path));
					// header('Content-Disposition: attachment; filename=' . $filename. '.tmp');
					// header('Content-Range: bytes=' . $filerange . '-' . ($filesize - 1) . '/' . ($filesize)); 
// 					
					// ob_end_flush();
					// fseek($fp, $filerange);
					// fpassthru($fp);
				// }
			// }
			// else {
				// StatusCode::setStatus(3004);
				// \CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
			// }
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
	
	/**
	 * Create File
	 */
	public function create($segments) {
		$params = \CLx\Core\Request::params();
		$files = \CLx\Core\Request::files();
		
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if($username = $this->auth_model->updateToken($token)) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);
			
			// Initialize SimFS
			SimFS::init(FILE_LOCATE);
			
			$path = $this->file_model->parsePath($segments);
			
			if(SimFS::isExists($path))
				StatusCode::setStatus(3007);
				
			if(NULL !== $files && !StatusCode::isError()) {
				// File Upload Handler
				if(0 != $files['error'])
					StatusCode::setStatus(3005);
				
				// Check capacity used
				if($files['size'] + SimFS::getUsed() > $this->file_config['capacity'])
					StatusCode::setStatus(4000);
				
				if(!StatusCode::isError()) {
					// SimFS Create File
					if(!SimFS::create($path, $files['tmp_name']))
						StatusCode::setStatus(3006);
				}
				
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
				if(!SimFS::create($path))
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
// 			// Initialize SimFS
			// SimFS::init(FILE_LOCATE);
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
	// public function delete($segments) {
		// $params = \CLx\Core\Request::params();
// 		
		// $token = isset($params['token']) ? $params['token'] : NULL;
// 
		// if($username = $this->auth_model->updateToken($token)) {
			// define('FILE_LOCATE', $this->file_config['locate'] . $username);
//
// 			// Initialize SimFS
			// SimFS::init(FILE_LOCATE);
// 			
			// $path = $this->file_model->parsePath($segments);
			// $current_path = FILE_LOCATE . $path;
// 			
			// if(file_exists($current_path) && $path != '') {
				// if(is_dir($current_path)) {
					// if(!$this->file_model->recursiveRmdir($current_path))
						// StatusCode::setStatus(3006);
// 					
					// if(!StatusCode::isError()) {
						// \CLx\Core\Event::trigger('file_change', array(
							// 'user' => $username,
							// 'token' => $token,
							// 'send' => array(
								// 'action' => 'delete',
								// 'type' => 'dir',
								// 'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							// )
						// ));
					// }
				// }
				// else {
					// if(!unlink($current_path))
						// StatusCode::setStatus(3006);
// 					
					// if(!StatusCode::isError()) {
						// \CLx\Core\Event::trigger('file_change', array(
							// 'user' => $username,
							// 'token' => $token,
							// 'send' => array(
								// 'action' => 'delete',
								// 'type' => 'file',
								// 'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							// )
						// ));
					// }
				// }
			// }
			// else
				// StatusCode::setStatus(3004);
		// }
// 
		// \CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	// }
}
