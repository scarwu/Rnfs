<?php
/**
 * RNFileSystem Config
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/RNFileSystem
 */

$Config['auth'] = array(
	'timeout' => 300,
	'connect' => 10
);

$Config['file'] = array(
	'locate' => TEMP_DIR . 'Rnfs.Server/',
	'encode' => PHP_OS == 'Linux' ? 'UTF-8' : 'BIG5',
	'upload_limit' => 268435456,
	'capacity' => 1073741824,
	'revert' => 1,
	'backup' => 2
);

$Config['sync'] = array(
	'timeout' => 180,
	'locate' => TEMP_DIR . 'Rnfs.Server/'
);
