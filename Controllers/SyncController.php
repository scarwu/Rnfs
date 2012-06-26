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

class SyncController extends \CLx\Core\Controller {
	public function __construct() {
		parent::__construct();
		
		// Load Config
		$this->sync_config = \CLx\Core\Loader::config('Config', 'sync');
		
		// Load Model
		$this->auth_model = \CLx\Core\Loader::model('Auth');
		
		// Load Extend Library
		\CLx\Core\Loader::library('StatusCode');
	}

	public function create($segments) {
		$params = \CLx\Core\Request::params();
		
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		$token = isset($params['token']) ? $params['token'] : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);

		if($username == $this->auth_model->updateToken($token)) {
			//FIXME
			\CLx\Library\Database::disconnect();
			
			if(!file_exists($this->sync_config['locate'] . $username))
				@mkdir($this->sync_config['locate'] . $username, 0755, TRUE);
			
			$sockpath = $this->sync_config['locate'] . $username . DIRECTORY_SEPARATOR . $token;
			
			$startTime = time();
			set_time_limit($this->sync_config['timeout']+5);
			
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
				elseif(time()-$startTime >= $this->sync_config['timeout']) {
					\CLx\Core\Response::setCode(408);
					break;
				}
			}
		}
		else
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}