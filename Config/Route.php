<?php

/**
 * Route Rule Setting
 * 
 * @var array
 */
$Route['get'] = array(
	array('/:string', function($result) {
		\CLx\Core\Loader::Controller($result[0], 'read');
	}),
	array('/:string', function($result) {
		\CLx\Core\Loader::Controller($result[0], 'index');
	}),
	array('default', function() {
		\CLx\Core\Loader::Controller('service', 'index');
	})
);

$Route['post'] = array(
	array('/:string', function($result) {
		\CLx\Core\Loader::Controller($result[0], 'create');
	}),
	array('default', function() {
		\CLx\Core\Loader::Controller('service', 'index');
	})
);

$Route['put'] = array(
	array('/:string', function($result) {
		\CLx\Core\Loader::Controller($result[0], 'update');
	}),
	array('default', function() {
		\CLx\Core\Loader::Controller('service', 'index');
	})
);

$Route['delete'] = array(
	array('/:string', function($result) {
		\CLx\Core\Loader::Controller($result[0], 'delete');
	}),
	array('default', function() {
		\CLx\Core\Loader::Controller('service', 'index');
	})
);
