<?php
/**
 * Reborn Authentication API Usage
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

$Usage['auth'] = array(
	'POST' => array(
		array(
			'description' => 'Verify identity and obtain token.',
			'API' => '/auth',
			'input' => array(
				array('username', 'string', 24),
				array('password', 'string', 24)
			),
			'output' => array(
				array('status', 'array'),
				array('token', 'string', 64)
			)
		)
	),
	'PUT' => array(
		array(
			'description' => 'Check token and extend token alive time.',
			'API' => '/auth',
			'input' => array(
				array('token', 'string', 64)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	),
	'DELETE' => array(
		array(
			'description' => 'Cancel authentication.',
			'API' => '/auth',
			'input' => array(
				array('token', 'string', 64)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	)
);
