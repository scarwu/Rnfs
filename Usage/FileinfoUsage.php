<?php
/**
 * RNFileSystem File Information Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['fileinfo'] = array(
	'GET' => array(
		array(
			'description' => 'Get file information.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'Access-Token' => array('string', 64)
				)
			),
			'response' => array(
				'json' => array()
			)
		)
	)
);
