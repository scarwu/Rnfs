<?php

namespace CLxApp\Controllers;

class Filehash extends \CLx\Core\Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		print_r($_SERVER);
	}
}
