<?php
/**
 * File Information Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

$usage['fileinfo'] = array(
	'GET' => array(
		array(
			'description' => 'Get file information.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => array(
				'json' => array()
			)
		)
	)
);
