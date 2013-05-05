<?php
/**
 * WSDL Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

class WsdlController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get WSDL
	 */
	public function read($segments) {
		// Load Usage
		if($handle = @opendir(CLX_APP_ROOT . 'Usage')) {
			while($file = readdir($handle))
				if(is_file(CLX_APP_ROOT . 'Usage/' . $file))
					require_once CLX_APP_ROOT . 'Usage/' . $file;
			closedir($handle);
		}
		
		header('Content-Type: application/xml; charset=utf-8');
		
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<wsdl:description xmlns:wsdl="http://www.w3.org/ns/wsdl">';

		echo '<wsdl:types>';
		echo '</wsdl:types>';
		
		echo '<wsdl:interface>';
		echo '</wsdl:interface>';
		
		echo '<wsdl:binding>';
		echo '</wsdl:binding>';
		
		echo '<wsdl:service>';
		echo '</wsdl:service>';
		
		echo '</wsdl:description>';
	}
}
