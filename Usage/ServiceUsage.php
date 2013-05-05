<?php
/**
 * Services API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
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
