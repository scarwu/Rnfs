<?php
/**
 * Reborn Config
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

$Config['auth'] = array(
	'timeout' => 300,
	'connect' => 25
);

$Config['file'] = array(
	'locate' => TEMP_DIR . 'nanotube' . DIRECTORY_SEPARATOR,
	'encode' => PHP_OS == 'Linux' ? 'UTF-8' : 'BIG5',
	'size' => 256 * 1024 * 1024,
	'capacity' => 512 * 1024 * 1024
);

$Config['sync'] = array(
	'timeout' => 180,
	'locate' => TEMP_DIR . 'nanotube.sync' . DIRECTORY_SEPARATOR
);