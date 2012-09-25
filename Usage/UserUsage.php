<?php
/**
 * RNFileSystem User path Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['user'] = array(
	'GET' => array(
		array(
			'description' => 'Obtain user information.',
			'path' => '/user/{username}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => array(
				'json' => array(
					'username' => array('string'),
					'email' => array('string'),
					'upload_limit' => array('integer'),
					'capacity' => array('integer'),
					'used' => array('integer')
				)
			)
		)
	),
	'POST' => array(
		array(
			'description' => 'Register a new user account.',
			'path' => '/user/{username}',
			'request' => array(
				array('password', 'string', 32),
				array('email', 'string', 32)
			),
			'response' => NULL
		)
	),
	'PUT' => array(
		array(
			'description' => 'Update user information.',
			'path' => '/user/{username}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				),
				'json' => array(
					'old_password' => array('string', 24),
					'new_password' => array('string', 24)
				)
			),
			'response' => NULL
		)
	),
	'DELETE' => array(
		array(
			'description' => 'It will destroy user, be careful.',
			'path' => '/user/{username}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				),
				'json' => array(
					'password' => array('string', 24)
				)
			),
			'response' => NULL
		)
	)
);
