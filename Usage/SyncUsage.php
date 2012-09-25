<?php
/**
 * RNFileSystem Sync API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['sync'] = array(
	'POST' => array(
		array(
			'description' => 'Create Server push.',
			'path' => '/sync/{username}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => array(
				'json' => array(
					'action' => array('string'),
					'type' => array('string'),
					'path' => array('steing')
				)
			)
		)
	)
);
