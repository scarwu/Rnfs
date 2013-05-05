<?php
/**
 * Sync API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
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
