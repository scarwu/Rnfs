<?php

$Schema['accounts'] = array(
	'table' => array(
		'id' => array(),
		'username' => array(),
		'password' => array(),
		'email' => array()
	),
	'primary' => 'id',
	'charset' => 'utf8',
	'imcrement' => 1
);

$Schema['tokenlist'] = array(
	'table' => array(
		'token' => array(),
		'username' => array(),
		'timestamp' => array()
	),
	'primary' => 'token',
	'charset' => 'utf8'
);

// CREATE TABLE IF NOT EXISTS `accounts` (
  // `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  // `username` varchar(24) NOT NULL,
  // `password` varchar(32) NOT NULL,
  // `email` varchar(32) NOT NULL,
  // PRIMARY KEY (`id`)
// ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
// 
// CREATE TABLE IF NOT EXISTS `tokenlist` (
  // `token` varchar(64) NOT NULL,
  // `username` varchar(24) NOT NULL,
  // `timestamp` int(10) unsigned NOT NULL,
  // PRIMARY KEY (`token`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8;