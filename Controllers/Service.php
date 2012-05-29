<?php

namespace CLxApp\Controllers;

class Service extends \CLx\Core\Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		print_r($_SERVER);
	}
}
