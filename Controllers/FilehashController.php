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

class FilehashController extends \CLx\Core\Controller {

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
			$local_hash = isset($params['hash']) ? $params['hash'] : NULL;
			
			define('FILE_LOCATE', $this->file_config['locate'] . $username);

			if(!file_exists(FILE_LOCATE))
				mkdir(FILE_LOCATE, 0755, TRUE);

			$path = $this->file_model->parsePath($segments);
			
			if(file_exists(FILE_LOCATE . $path)) {
				if(is_dir(FILE_LOCATE . $path)) {
					$list = $this->file_model->getFileHashList($path);
					$jsonlist = '';
					foreach($list as $item) {
						if(isset($item['hash']))
							$jsonlist .= sprintf('{"hash":"%s","path":"%s"},', $item['hash'], $item['path']);
						else
							$jsonlist .= sprintf('{"path":"%s"},', $item['path']);
					}
					$jsonlist = sprintf('[%s]', trim($jsonlist, ','));
					$server_hash = hash('md5', $jsonlist);
				}
				else
					$server_hash = md5_file(FILE_LOCATE . $path);
				
				// Compare Hash
				if($local_hash != NULL)
					$sync = $local_hash == $server_hash;
				else
					$sync = FALSE;
				
				\CLx\Core\Response::toJSON(array(
					'status' => StatusCode::getStatus(),
					'sync' => $sync,
					'hash' => $server_hash
				));
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
