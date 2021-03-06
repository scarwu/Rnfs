<?php
/**
 * Route Rule Config
 * 
 * @package		RESTful Network File System
 * @author		ScarWu
 * @copyright	Copyright (c) 2012-2013, ScarWu (http://scar.simcz.tw/)
 * @license		https://github.com/scarwu/Rnfs/blob/master/LICENSE
 * @link		https://github.com/scarwu/Rnfs
 */

/**
 * Set Default Header
 */
header('Content-Type: none');

// Regular Expression
$regex_url = '/^\/(\w+)((?:\/(?:.|\n)+)+)?/u';

/**
 * GET Method Route Rules
 * 
 * @var array
 */
$Route['get'] = array(
	array($regex_url, function($result) {
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
	array($regex_url, function($result) {
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
	array($regex_url, function($result) {
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
	array($regex_url, function($result) {
		$result[1] = isset($result[1]) ? explode('/', trim($result[1], '/')) : NULL;
		
		if(!\CLx\Core\Loader::controller($result[0], 'delete', $result[1]))
			\CLx\Core\Response::setCode(503);
	}, TRUE),
	array('default', function() {
		\CLx\Core\Response::setCode(404);
	})
);
