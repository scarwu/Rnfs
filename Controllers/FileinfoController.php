<?php
/**
 * Reborn File Information Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class FileinfoController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
		// Load Config
		$this->fileConfig = $this->load->config('file');
		// Load Library
		$this->load->sysLib('db');
		// Load Extend Library
		$this->load->extLib('statusCode');
		// Load Model
		$this->load->model('authModel');
		$this->load->model('fileModel');
	}

	public function read() {
		$segments = request::segments();
		$params = request::params();
		
		$token = isset($params['token']) ? $params['token'] : NULL;

		if($username = $this->authModel->updateToken($token)) {
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$path = $this->fileModel->parsePath($segments);

			if(file_exists($currentPath = FILE_LOCATE . $path)) {
				$info = array(
					'path' => iconv(mb_detect_encoding($path), $this->fileConfig['encode'], $path),
					'date' => @filectime($currentPath),
					'size' => @filesize($currentPath),
					'type' => @filetype($currentPath)
				);

				$this->view->json(array('status' => statusCode::getStatus(), 'info' => $info));
			}
			else {
				statusCode::setStatus(3004);
				$this->view->json(array('status' => statusCode::getStatus()));
			}
		}
		else
			$this->view->json(array('status' => statusCode::getStatus()));
	}
}
