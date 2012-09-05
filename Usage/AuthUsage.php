<?php
/**
 * RNFileSystem Authentication path Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['auth'] = array(
	'POST' => array(
		array(
			'description' => 'Verify identity and obtain token.',
			'path' => '/auth',
			'request' => array(
				'json' => array(
					'username' => array('string', 24),
					'password' => array('string', 24),
					'encrypt' => array('boolean')
				)
			),
			'response' => array(
				'json' => array(
					'token' => array('string', 64)
				)
			)
		)
	),
	'PUT' => array(
		array(
			'description' => 'Check token and extend token alive time.',
			'path' => '/auth',
			'request' => array(
				'header' => array(
					'Access-Token' => array('string', 64)
				)
			),
			'response' => NULL
		)
	),
	'DELETE' => array(
		array(
			'description' => 'Cancel authentication.',
			'path' => '/auth',
			'request' => array(
				'header' => array(
					'Access-Token' => array('string', 64)
				)
			),
			'response' => NULL
		)
	)
);
