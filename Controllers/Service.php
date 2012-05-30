<?php
/**
 * 
 */

class Service extends \CLx\Core\Controller {
	
	/**
	 * 
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * 
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
			array_push($list, $key);
		sort($list);

		// Send Json
		\CLx\Core\Response::toJSON(array(
			'status' => statusCode::getStatus(),
			'list' => $list,
			'usage' => $usage,
			'statuscode' => statusCode::getStatusList()
		));
	}
}
