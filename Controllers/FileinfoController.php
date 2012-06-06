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
		$this->file_config = \CLx\Core\Loader::config('Config', 'file');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		$this->file_model = \CLx\Core\Loader::model('File');
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
	}

	public function read($segments) {
		$params = \CLx\Core\Request::params();
		
		$token = isset($params['token']) ? $params['token'] : NULL;

		if($username = $this->auth_model->updateToken($token)) {
			define('FILE_LOCATE', $this->file_config['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);
			
			$path = $this->file_model->parsePath($segments);

			if(file_exists($current_path = FILE_LOCATE . $path)) {
				$info = array(
					'path' => iconv(mb_detect_encoding($path), $this->file_config['encode'], $path),
					'date' => @filectime($current_path),
					'size' => @filesize($current_path),
					'type' => @filetype($current_path)
				);

				\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus(), 'info' => $info));
			}
			else {
				StatusCode::setStatus(3004);
				\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
			}
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
