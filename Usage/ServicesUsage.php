<?php
/**
 * Reborn Services API Usage
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

$Usage['services'] = array(
	'GET' => array(
		array(
			'description' => 'Obtain services list and services usage.',
			'API' => '/services',
			'input' => array(
				array('full', 'boolean')
			),
			'output' => array(
				array('status', 'array'),
				array('list', 'array'),
				array('usage', 'array'),
				array('statuscode', 'array')
			)
		)
	)
);
