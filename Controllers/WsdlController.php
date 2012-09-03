<?php
/**
 * RNFileSystem WSDL Controller
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

class WsdlController extends \CLx\Core\Controller {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Get WSDL
	 */
	public function read($segments) {
		header('Content-Type: application/xml; charset=utf-8');
		
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<description>';

		echo '<types>';
		echo '</types>';
		
		echo '<interface>';
		echo '</interface>';
		
		echo '<binding>';
		echo '</binding>';
		
		echo '<service>';
		echo '</service>';
		
		echo '</description>';
	}
}
