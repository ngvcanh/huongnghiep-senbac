<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;

final class Request{

	private static $request;
	private $encoded;
	private $files;
	private $post;
	private $get;
	private $url;

	private function __construct(){}

	static function init(){
		$rq = self::$request;
		if (!isset($rq)) $rq = new Request();
		if (!$rq->encoded){
			$rq->get 	= $rq->encode($_GET);
			$rq->post 	= $rq->encode($_POST);
			$rq->files 	= $rq->encode($_FILES);

			$rq->emptyRequest();
			$rq->encoded = true;

			$rq->splitURL();
		}
		return $rq;
	}

	function encode($param){
		$paramType = gettype($param);
		switch ($paramType){
			case 'object':
				if (!is_callable($param)) foreach ($param as $key => $value) $param->{$key} = $this->encode($value);
				break;
			case 'array':
				foreach ($param as $key => $value) $param[$key] = $this->encode($value);
				break;
			case 'string':
				$param = htmlentities($param);
				break;
		}
		return $param;
	}

	function decode($param){
		$paramType = gettype($param);
		switch ($paramType){
			case 'object':
				if (!is_callable($param)) foreach ($param as $key => $value) $param->{$key} = $this->decode($value);
				break;
			case 'array':
				foreach ($param as $key => $value) $param[$key] = $this->decode($value);
				break;
			case 'string':
				$param = html_entity_decode($param);
				break;
		}
		return $param;
	}

	function getGet(){
		return $this->get;
	}

	function getPost(){
		return $this->post;
	}

	function getFiles(){
		return $this->files;
	}

	function getUrl(){
		return $this->url;
	}

	private function splitURL(){
		$this->url = new \stdClass;
		$this->url->get = $this->get;

		$currentURL = URL::getCurrent();
		$project	= URL::getProject();

		$pattern 	= "#^{$project}#";
		$arrURL		= explode('?', $currentURL);
		$path		= preg_replace($pattern, '', $arrURL[0]);

		$this->url->dir = (!!$path && $path != '/') ? explode('/', trim($path, '/')) : [];
	}

	private function emptyRequest(){
		$_GET 	= [];
		$_POST 	= [];
		$_FILES = [];
	}
}