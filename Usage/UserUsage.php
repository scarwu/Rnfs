<?php
/**
 * RNFileSystem User API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['user'] = array(
	'POST' => array(
		array(
			'description' => 'Register a new user account.',
			'API' => '/user/{username}',
			'input' => array(
				array('password', 'string', 32),
				array('email', 'string', 32)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	),
	'GET' => array(
		array(
			'description' => 'Obtain user information.',
			'API' => '/user/{username}',
			'input' => array(
				array('token', 'string', 64)
			),
			'output' => array(
				array('status', 'array'),
				array('email', 'string', 24)
			)
		)
	),
	'PUT' => array(
		array(
			'description' => 'Update user information.',
			'API' => '/user/{username}',
			'input' => array(
				array('token', 'string', 64),
				array('old_password', 'string', 24),
				array('new_password', 'string', 24)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	),
	'DELETE' => array(
		array(
			'description' => 'It will destroy user, be careful.',
			'API' => '/user/{username}',
			'input' => array(
				array('token', 'string', 64),
				array('password', 'string', 24)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	)
);
