<?php
/**
 * Reborn Route Rule Config
 * 
 * @package		Reborn File Services
 * @author		ScarWu
 * @copyright	Copyright (c) 2012, ScarWu (http://scar.simcz.tw/)
 * @license		http://opensource.org/licenses/MIT Open Source Initiative OSI - The MIT License (MIT):Licensing
 * @link		http://github.com/scarwu/Reborn
 */

/**
 * GET Method Route Rules
 * 
 * @var array
 */
$Route['get'] = array(
	array('^/(\w+)((?:/[\w|\.]+)+)?', function($result) {
		$result[1] = isset($result[1]) ? explode('/', trim($result[1], '/')) : NULL;
		
		if(!\CLx\Core\Loader::controller($result[0], 'read', $result[1]))
			\CLx\Core\Response::setCode(503);
	}, TRUE),
	array('/', function() {
		\CLx\Core\Loader::view('index');
	}),
	array('default', function() {
		\CLx\Core\Response::setCode(404);
	})
);

/**
 * POST Method Route Rules
 * 
 * @var array
 */
$Route['post'] = array(
	array('^/(\w+)((?:/[\w|\.]+)+)?', function($result) {
		$result[1] = isset($result[1]) ? explode('/', trim($result[1], '/')) : NULL;
		
		if(!\CLx\Core\Loader::controller($result[0], 'create', $result[1]))
			\CLx\Core\Response::setCode(503);
	}, TRUE),
	array('default', function() {
		\CLx\Core\Response::setCode(404);
	})
);

/**
 * PUT Method Route Rules
 * 
 * @var array
 */
$Route['put'] = array(
	array('^/(\w+)((?:/[\w|\.]+)+)?', function($result) {
		$result[1] = isset($result[1]) ? explode('/', trim($result[1], '/')) : NULL;
		
		if(!\CLx\Core\Loader::controller($result[0], 'update', $result[1]))
			\CLx\Core\Response::setCode(503);
	}, TRUE),
	array('default', function() {
		\CLx\Core\Response::setCode(404);
	})
);

/**
 * DELETE Method Route Rules
 * 
 * @var array
 */
$Route['delete'] = array(
	array('^/(\w+)((?:/[\w|\.]+)+)?', function($result) {
		$result[1] = isset($result[1]) ? explode('/', trim($result[1], '/')) : NULL;
		
		if(!\CLx\Core\Loader::controller($result[0], 'delete', $result[1]))
			\CLx\Core\Response::setCode(503);
	}, TRUE),
	array('default', function() {
		\CLx\Core\Response::setCode(404);
	})
);
