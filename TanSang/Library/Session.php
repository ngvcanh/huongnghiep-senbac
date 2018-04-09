<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

final class Session{

	static function init(){
		session_start();
	}

	static function set($key, $value){
		$_SESSION[K_SITE_APP][$key] = $value;
	}

	static function add($key, $value){
		$current 	= self::get($key);
		$cur 		= is_object($current) ? clone $current : $current;

		if ($cur == NULL) $current = [];
		if (is_array($current)) $current[] = $value;
		else $current = [$cur, $value];

		self::set($key, $current);
	}

	static function get($key){
		return isset($_SESSION[K_SITE_APP][$key]) ? $_SESSION[K_SITE_APP][$key] : NULL;
	}

	static function cancel($key){
		unset($_SESSION[K_SITE_APP][$key]);
	}

	static function remove($key, $value = NULL){
		if ($value !== NULL){
			$current = self::get($key);
			if ($current != NULL && !is_array($current)) self::cancel($key);
			if (is_array($current)){
				$index = array_search($value, $current);
				if ($index >= 0) unset($current[$index]);
				self::set($key, $current);
			}
		}
		else self::cancel($key);
	}

	static function destroy(){
		session_destroy();
	}

}