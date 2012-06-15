<?php
/**
 * Reborn Sync API Usage
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

$usage['sync'] = array(
	'POST' => array(
		array(
			'description' => 'Create Server push.',
			'API' => '/sync/{username}',
			'input' => array(
				array('token', 'string', 24)
			),
			'output' => array(
				array('status', 'array'),
				array('action', 'string'),
				array('type', 'string'),
				array('path', 'steing')
			)
		)
	)
);
