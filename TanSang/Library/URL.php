<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

final class URL{

	static function getCurrent(){
		return $_SERVER['REQUEST_URI'];
	}

	static function getProject(){
		$script = explode('/', self::getScriptName());
		array_pop($script);
		$result = '/' . join('/', $script);
		return preg_replace('/^\/\//', '/', $result);
	}

	static function getQueryString(){
		return $_SERVER['QUERY_STRING'];
	}

	static function getScriptName(){
		return $_SERVER['SCRIPT_NAME'];
	}

	static function create($dir = [], $get = [], $fragment = NULL){
		$url = '';

		if (is_string($dir)) $url .= $dir;
		else if (is_array($dir)) foreach ($dir as $value) $url .= '/' . $value;

		if (is_string($get)) $url .= $get;
		else if (is_array($get) && !empty($get)){
			$url 	.= '?';
			$arrGet = [];

			foreach ($get as $key => $value) $arrGet[] = $key . '=' . $value;
			$url .= join('&', $arrGet);
		}

		if (!!$fragment && is_string($fragment)) $url .= '#' . $fragment;
		$url = self::getProject() . $url;

		return preg_replace('/^\/\//', '/', $url);
	}

	static function redirect($url){
		header('Location: ' . $url);
		die;
	}

	static function remote($url, array $data = []){
		$result = false;

		if (is_callable('curl_exec')){
			$ch = curl_init($url); 

			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			if (!empty($data)){
				curl_setopt($ch, CURLOPT_POST, count($data));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}

			curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Requested-With: XMLHttpRequest"]);
			$result = curl_exec($ch);

			curl_close($ch);
		}

		return $result;
	}
}