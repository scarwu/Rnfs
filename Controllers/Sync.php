<?php
/**
 * Reborn Sync Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

class Sync extends \CLx\Core\Controller {
	public function __construct() {
		parent::__construct();
		// Load Config
		$this->syncConfig = $this->load->config('sync');
		// Load Library
		$this->load->sysLib('db');
		// Load Extend Library
		$this->load->extLib('statusCode');
		// Load Model
		$this->load->model('authModel');
	}

	public function create() {
		$segments = request::segments();
		$params = request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			statusCode::setStatus(2001);

		if($username == $this->authModel->updateToken($token)) {
			//FIXME
			db::disconnect();
			
			if(!file_exists($this->syncConfig['locate'] . $username))
				@mkdir($this->syncConfig['locate'] . $username, 0755, TRUE);
			
			$sockpath = $this->syncConfig['locate'] . $username . DIRECTORY_SEPARATOR . $token;
			
			$startTime = time();
			set_time_limit($this->syncConfig['timeout']+5);
			
			if(file_exists($sockpath))
				unlink($sockpath);

			while(1) {
				if(PHP_OS == 'Linux')
					usleep(1000);
				else
					sleep(1);
				
				if(file_exists($sockpath)) {
					$result = NULL;
					$handle = fopen($sockpath, 'r');
					while($data = fread($handle, 1024))
						$result .= $data;
					fclose($handle);
					unlink($sockpath);
					header('Content-Length: ' . strlen($result));
					echo $result;
					break;
				}
				elseif(time()-$startTime >= $this->syncConfig['timeout']) {
					response::sendHeader(408);
					break;
				}
			}
		}
		else
			$this->view->json(array('status' => statusCode::getStatus()));
	}
}
