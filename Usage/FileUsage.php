<?php
/**
 * RNFileSystem File API Usage
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$usage['file'] = array(
	'POST' => array(
		array(
			'description' => 'Upload file or make directory.',
			'API' => '/file/{filepath or dirpath}',
			'input' => array(
				array('token', 'string', 64),
				array('File content')
			),
			'output' => array(
				array('status', 'array')
			)
		)
	),
	'GET' => array(
		array(
			'description' => 'Download file.',
			'API' => '/file/{filepath}',
			'input' => array(
				array('token', 'string', 64),
				array('filerange', 'int')
			),
			'output' => array(
				array('status', 'array'),
				array('File content')
			)
		)
	),
	'PUT' => array(
		array(
			'description' => 'Update directory or file.',
			'API' => '/file/{filepath or dirpath}',
			'input' => array(
				array('token', 'string', 64),
				array('newpath', 'string'),
				array('File content')
			),
			'output' => array(
				array('status', 'array')
			)
		)
	),
	'DELETE' => array(
		array(
			'description' => 'Delete the directory or the file.',
			'API' => '/file/{filepath or dirpath}',
			'input' => array(
				array('token', 'string', 64)
			),
			'output' => array(
				array('status', 'array')
			)
		)
	)
);
