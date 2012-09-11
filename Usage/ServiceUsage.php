<?php
/**
 * RNFileSystem Services API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['service'] = array(
	'GET' => array(
		array(
			'description' => 'Obtain service list and service usage.',
			'path' => '/service',
			'request' => NULL,
			'response' => array(
				'json' => array(
					'list' => array('array'),
					'usage' => array('array'),
					'status_code' => array('array')
				)
			)
		)
	)
);
