<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

function prr($var){
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}
/*
function __autoload($className){
	$className 	= str_replace('\\', K_DS, $className);
	$filePath 	= K_ROOT . $className . '.php';
	if (is_file($filePath)) include_once $filePath;
}*/
spl_autoload_register(function($className){
	$className 	= str_replace('\\', K_DS, $className);
	$filePath 	= K_ROOT . $className . '.php';
	if (is_file($filePath)) include_once $filePath;
});