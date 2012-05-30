<?php
/**
 * Reborn Filehash Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class Filehash extends \CLx\Core\Controller {

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
			$localHash = isset($params['hash']) ? $params['hash'] : NULL;
			
			define('FILE_LOCATE', $this->fileConfig['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);

			$path = $this->fileModel->parsePath($segments);
			
			if(file_exists(FILE_LOCATE . $path)) {
				if(is_dir(FILE_LOCATE . $path)) {
					$list = $this->fileModel->getFileHashList($path);
					$jsonlist = '';
					foreach($list as $item) {
						if(isset($item['hash']))
							$jsonlist .= sprintf('{"hash":"%s","path":"%s"},', $item['hash'], $item['path']);
						else
							$jsonlist .= sprintf('{"path":"%s"},', $item['path']);
					}
					$jsonlist = sprintf('[%s]', trim($jsonlist, ','));
					$serverHash = hash('md5', $jsonlist);
				}
				else
					$serverHash = md5_file(FILE_LOCATE . $path);
				
				// Compare Hash
				if($localHash != NULL)
					$sync = $localHash == $serverHash;
				else
					$sync = FALSE;
				
				$this->view->json(array(
					'status' => statusCode::getStatus(),
					'sync' => $sync,
					'hash' => $serverHash
				));
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
