<?php
/**
 * File path Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

$usage['file'] = array(
	'GET' => array(
		array(
			'description' => 'Download file.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => array(
				'json' => array(),
				'file_content' => TRUE
			)
		)
	),
	'POST' => array(
		array(
			'description' => 'Upload file or make directory.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				),
				'file_content' => TRUE
			),
			'response' => NULL
		)
	),
	'PUT' => array(
		array(
			'description' => 'Update directory or file.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				),
				'json' => array(
					'path' => array('string')
				),
				'file_content' => TRUE
			),
			'response' => NULL
		)
	),
	'DELETE' => array(
		array(
			'description' => 'Delete the directory or the file.',
			'path' => '/file/{filepath}',
			'request' => array(
				'header' => array(
					'X-Rnfs-Token' => array('string', 64)
				)
			),
			'response' => NULL
		)
	)
);
