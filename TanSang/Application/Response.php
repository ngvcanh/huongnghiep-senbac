<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Json;

final class Response{

	private $header = 'text/html';
	private $data;

	function __construct($header, \Closure $callback = NULL){
		$this->setHeader($header);
		if ($callback != NULL) $this->setData($callback())->display();
	}

	function setData($data){
		$this->data = $data;
		return $this;
	}

	function setHeader($header){
		$this->header = $header;
	}

	function display(){
		$header = $this->header;
		if (preg_match('/^Content\-Type/misU', $header)) $header .= ';charset=UTF-8';
		header($header);

		$content = $this->data;
		if (is_array($content) || is_object($content)) $content = Json::toTree($content, 0, 0, 0);

		print($content);
		die;
	}

}