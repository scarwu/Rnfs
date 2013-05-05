<?php
/**
 * Authentication path Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
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
					'X-Rnfs-Token' => array('string', 64)
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
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => NULL
		)
	)
);
