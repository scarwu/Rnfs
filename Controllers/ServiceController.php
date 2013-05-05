<?php
/**
 * Service Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class ServiceController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
		
		// Load Library
		\CLx\Core\Loader::library('StatusCode');
	}
	
	/**
	 * Load service list
	 */
	public function read() {
		// Load Usage
		if($handle = @opendir(CLX_APP_ROOT . 'Usage')) {
			while($file = readdir($handle))
				if(is_file(CLX_APP_ROOT . 'Usage/' . $file))
					require_once CLX_APP_ROOT . 'Usage/' . $file;
			closedir($handle);
		}
		
		// Create List
		$list = array_keys($usage);
		sort($list);
		
		// Send Json
		\CLx\Core\Response::toJSON(array(
			'list' => $list,
			'usage' => $usage,
			'code' => StatusCode::getStatusList()
		));
	}
}
