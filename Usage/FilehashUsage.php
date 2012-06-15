<?php
/**
 * Reborn File List API Usage
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

$usage['filehash'] = array(
	'GET' => array(
		array(
			'description' => 'Compare file hash.',
			'API' => '/filehash/{filepath}',
			'input' => array(
				array('token', 'string', 64),
				array('hash', 'string', 32)
			),
			'output' => array(
				array('status', 'array'),
				array('hash', 'string', 32),
				array('sync', 'boolean')
			)
		)
	)
);
