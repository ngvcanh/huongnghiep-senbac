<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;

final class Controller{

	private static $controller;
	private $view;
	private $model;
	private $request;

	private function __construct(){}

	static function init(){
		if (!isset(self::$controller)) self::$controller = new Controller();
		return self::$controller->initRequest()->initView();
	}

	function run(){
		$this->createTree()->view->render($this->request);
	}

	private function initRequest(){
		if (!isset($this->request)) $this->request = Request::init();
		return $this;
	}

	private function initView(){
		if (!isset($this->view)) $this->view = View::init();
		return $this;
	}

	private function createTree(){
		File::mkDir(K_ROOT . K_DIR_MODULE);
		File::mkDir(K_ROOT . K_DIR_THEME);
		return $this;
	}
}