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
	private $locate;
	private $encode;

	public function __construct() {
		parent::__construct();
		// Load Config
		$this->fileConfig = $this->load->config('file');
		// Load Library
		$this->load->sysLib('db');
		$this->load->sysLib('event');
		// Load Extend Library
		$this->load->extLib('statusCode');
		// Load Model
		$this->load->model('authModel');
		$this->load->model('fileModel');
	}
	
	/**
	 * Upload File or Make Directory
	 * Input:
	 * 		API: /files/{upload path.../upload file or directory name}
	 * 		params(apikey, token),
	 * 		POST file (form-data)
	 * Output:
	 * 		status: 200, 302, 404
	 * 
	 * 2011/12/21 CCLien
	 */
	public function create() {
		$segments = request::segments();
		$params = request::params();
		$files = request::files();
		
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if($username = $this->authModel->updateToken($token)) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);

			$path = $this->fileModel->parsePath($segments);
			$currentPath = FILE_LOCATE . $path;
			
			if(!file_exists($currentPath) && $path != '') {
				if($files != NULL) {
					// File Upload Handler
					if(0 != $files['error'])
						statusCode::setStatus(3005);
					
					if($files['size'] + $this->fileModel->getAllFileSize('/') > $this->fileConfig['capacity'])
						statusCode::setStatus(4000);
					
					if(!statusCode::isError()) {
						$dirpath = $segments;
						array_pop($dirpath);
						$dirpath = FILE_LOCATE . $this->fileModel->parsePath($dirpath);
						
						if(!file_exists($dirpath))
							mkdir($dirpath, 0755, TRUE);
						
						if(!move_uploaded_file($files['tmp_name'], $currentPath))
							statusCode::setStatus(3006);
					}
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'create',
								'type' => 'file',
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							)
						));
					}
				}
				else {
					// Create New Dir
					if(!mkdir($currentPath, 0755, TRUE))
						statusCode::setStatus(3006);
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'create',
								'type' => 'dir',
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							)
						));
					}
				}
			}
			else
				statusCode::setStatus(3007);
		}

		$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Get User's Directory List or Download a file.
	 * Input:
	 * 		API: /files/{download path}, 
	 * 		params(apikey, token)
	 * Output:
	 * 		status: 200, 404
	 * 		{dirlist: {path, date, size, type}}
	 * 		{download file stream}
	 * 
	 * 2011/12/21 CCLien
	 */
	public function read() {
		$segments = request::segments();
		$params = request::params();
		
		$token = isset($params['token']) ? $params['token'] : NULL;

		if($username = $this->authModel->updateToken($token)) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$path = $this->fileModel->parsePath($segments);
			$currentPath = FILE_LOCATE . $path;
			
			if(file_exists($currentPath) && is_file($currentPath)) {
				$filerange = isset($params['filerange']) ? $params['filerange'] : NULL;

				$filesize = filesize($currentPath);
				$filename = $segments[count($segments) - 1];
				
				if($filerange == NULL) {
					header('Accept-Ranges: bytes');
					header('Content-Length: '. $filesize); 
					header('HTTP/1.1 200 OK'); 
					header('Content-Type: ' . $this->fileModel->getMimetype($currentPath));
					header('Content-Disposition: attachment; filename=' . $filename);
					
					ob_end_flush();
					readfile($currentPath);
				}
				else {
					$fp = fopen($currentPath, 'rb');
					
					header('Accept-Ranges: bytes');
					header('Content-Length: '. ($filesize - $range)); 
					header('HTTP/1.1 206 Partial Content'); 
					header('Content-Type: ' . $this->fileModel->getMimetype($currentPath));
					header('Content-Disposition: attachment; filename=' . $filename. '.tmp');
					header('Content-Range: bytes=' . $filerange . '-' . ($filesize - 1) . '/' . ($filesize)); 
					
					ob_end_flush();
					fseek($fp, $filerange);
					fpassthru($fp);
				}
			}
			else {
				statusCode::setStatus(3004);
				$this->view->json(array('status' => statusCode::getStatus()));
			}
		}
		else
			$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Update User's Directory or File.
	 * Input:
	 * 		API: /files/{Update path}, 
	 * 		params(apikey, token, rename),
	 * 		PUT file (form-data)
	 * Output:
	 * 		status: 200, 404
	 * 
	 * 2011/12/21 CCLien
	 */
	public function update() {
		$segments = request::segments();
		$params = request::params();
		$files = request::files();
		
		$token = isset($params['token']) ? $params['token'] : NULL;

		if($username = $this->authModel->updateToken($token)) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$path = $this->fileModel->parsePath($segments);
			$currentPath = FILE_LOCATE . $path;
			
			$newpath = isset($params['newpath']) ? $params['newpath'] : NULL;
			$newpath = explode('/', trim($newpath, '/'));
			$newpath = $this->fileModel->parsePath($newpath);
			
			if(file_exists($currentPath) && $path != '') {
				if($files != NULL) {
					if(0 != $files['error'] || md5_file($files['tmp_name']) == md5_file($currentPath))
						statusCode::setStatus(3005);
					
					if(($files['size'] - filesize($currentPath)) + $this->fileModel->getAllFileSize('/') > $this->fileConfig['capacity'])
						statusCode::setStatus(4000);
					
					if(!statusCode::isError()) {
						$dirpath = $segments;
						array_pop($dirpath);
						$dirpath = FILE_LOCATE . $this->fileModel->parsePath($dirpath);
						
						if(!file_exists($dirpath))
							mkdir($dirpath, 0755, TRUE);
						
						if(!unlink($currentPath) || !copy($files['tmp_name'], $currentPath))
							statusCode::setStatus(3006);
					}
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'update',
								'type' => 'file',
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path),
								'hash' => md5_file($currentPath)
							)
						));
					}
				}
				else if($newpath !== NULL && !file_exists(FILE_LOCATE . $newpath) ) {
					if(!rename($currentPath, FILE_LOCATE . $newpath))
						statusCode::setStatus(3006);
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'rename',
								'type' => is_dir(FILE_LOCATE . $newpath) ? 'dir' : 'file',
								'oldpath' => str_replace(DIRECTORY_SEPARATOR, '/', $path),
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $newpath)
							)
						));
					}
				}
			}
			else
				statusCode::setStatus(3007);
		}

		$this->view->json(array('status' => statusCode::getStatus()));
	}
	
	/**
	 * Delete the Directory or File.
	 * Input:
	 * 		API: /files/{delete path}, 
	 * 		params(apikey, token)
	 * Output:
	 * 		status: 200, 404
	 * 
	 * 2011/12/21 CCLien
	 */
	public function delete() {
		$segments = request::segments();
		$params = request::params();
		
		$token = isset($params['token']) ? $params['token'] : NULL;

		if($username = $this->authModel->updateToken($token)) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$path = $this->fileModel->parsePath($segments);
			$currentPath = FILE_LOCATE . $path;
			
			if(file_exists($currentPath) && $path != '') {
				if(is_dir($currentPath)) {
					if(!$this->fileModel->recursiveRmdir($currentPath))
						statusCode::setStatus(3006);
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'delete',
								'type' => 'dir',
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							)
						));
					}
				}
				else {
					if(!unlink($currentPath))
						statusCode::setStatus(3006);
					
					if(!statusCode::isError()) {
						event::trigger('fileChange', array(
							'user' => $username,
							'token' => $token,
							'send' => array(
								'action' => 'delete',
								'type' => 'file',
								'path' => str_replace(DIRECTORY_SEPARATOR, '/', $path)
							)
						));
					}
				}
			}
			else
				statusCode::setStatus(3004);
		}

		$this->view->json(array('status' => statusCode::getStatus()));
	}
}
