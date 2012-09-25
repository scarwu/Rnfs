<?php
/**
 * RNFileSystem File path Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
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
