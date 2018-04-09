<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Application\Response;

class ImageSource{

	private $source;
	private $data;
	private $mime;
	private $name;
	
	function __construct($imagePath){
		if (is_string($imagePath)) $this->loadSource($imagePath)->encode();
	}

	function getData(){
		return isset($this->data) ? ('data:' . $this->mime . ';base64,' . $this->data) : NULL;
	}

	function download(){
		if (isset($this->data)){
			header('Content-Disposition: attachment;filename="' . $this->name . '"');
			new Response('Content-Type: application/force-download', function(){
				return base64_decode($this->data);
			});
		}
	}

	function display(){
		if (isset($this->data)){
			new Response("Content-type: {$this->mime}", function(){
				return base64_decode($this->data);
			});
		}
	}

	private function url_exists($url){
		$status = false;

		if (is_callable('curl_exec')){
			$ch = curl_init($url); 

			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);

			$code 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($code == 200){
				$status = true;
				$this->mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			}
			curl_close($ch);
		}

		return $status;
	}

	private function loadSource($imagePath){
		if (File::exist($imagePath) || $this->url_exists($imagePath)){
			$this->name 	= basename($imagePath);
			$this->source 	= @file_get_contents($imagePath);
			if (!isset($this->mime)) $this->mime = mime_content_type($imagePath);
		}
		return $this;
	}

	private function encode(){
		if (isset($this->source)) $this->data = base64_encode($this->source);
	}
}