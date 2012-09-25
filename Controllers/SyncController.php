<?php
/**
 * RNFileSystem Sync Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
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
		$headers = \CLx\Core\Request::headers();
		$params = \CLx\Core\Request::params();
		
		$token = isset($headers['X-Rnfs-Token']) ? $headers['X-Rnfs-Token'] : NULL;
		$username = !empty($segments[0]) ? strtolower($segments[0]) : NULL;
		
		if(NULL == $username)
			StatusCode::setStatus(2001);

		if($username == $this->auth_model->updateToken($token)) {
			// Database Disconnect
			\CLx\Library\Database::disconnect();
			
			if(!file_exists($this->sync_config['locate'] . $username . '/sync'))
				@mkdir($this->sync_config['locate'] . $username . '/sync', 0755, TRUE);
			
			$sync_path = $this->sync_config['locate'] . $username . '/sync/' . $token;
			
			$start_time = time();
			set_time_limit($this->sync_config['timeout']+5);
			
			// if(file_exists($sync_path))
				// unlink($sync_path);
			
			$loop = TRUE;
			while($loop) {
				// Sleep and wait 1 second
				sleep(1);
				
				if(file_exists($sync_path)) {
					// Wait Event Write-in file
					// sleep(2);
					
					$handle = fopen($sync_path, 'r');
					while(1)
						if(flock($handle, LOCK_EX)) {
							$result = NULL;
							while($data = fread($handle, 1024))
								$result .= $data;
							fflush($handle);
							break;
						}
					flock($handle, LOCK_UN);
					fclose($handle);
					
					$result = sprintf("[%s]", trim($result, ','));
					
					if(file_exists($sync_path))
						unlink($sync_path);
					
					header('Content-Length: ' . strlen($result));
					echo $result;
					$loop = FALSE;
				}
				elseif(time()-$start_time >= $this->sync_config['timeout']) {
					\CLx\Core\Response::setCode(408);
					$loop = FALSE;
				}
			}
		}
		
		if(StatusCode::isError())
			\CLx\Core\Response::toJSON(array('status' => StatusCode::getStatus()));
	}
}
