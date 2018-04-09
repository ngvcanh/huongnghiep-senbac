<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Application\Model;

class Main extends Model{

	function __construct(){
		parent::__construct();
	}

	public function listPost(){
		return parent::getMany('post', ['name', 'slug']);
	}
}