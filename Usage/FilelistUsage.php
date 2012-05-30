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

$Usage['filelist'] = array(
	'GET' => array(
		array(
			'description' => 'Obtain file information.',
			'API' => '/fileinfo/{filepath}',
			'input' => array(
				array('token', 'string', 64)
			),
			'output' => array(
				array('status', 'array'),
				array('list', 'array')
			)
		)
	)
);
