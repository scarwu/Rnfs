<?php
/**
 * Reborn Service Controller
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
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
		$list = array();
		foreach((array)$usage as $key => $value)
			$list[] = $key;
		sort($list);
		
		// Send Json
		\CLx\Core\Response::toJSON(array(
			'list' => $list,
			'usage' => $usage,
			'code' => StatusCode::getStatusList()
		));
	}
}
