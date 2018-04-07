<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\Session;
use Library\URL;

Session::init();

$module = 'Home';
$action = 'Home';

$urlHome 		= URL::create(['', K_URL_DASH]);
$urlCurrent = URL::getCurrent();
$urlProject = URL::getProject();

if ($this->configIsFirstDir()){
	if (isset($dirURL[0])){
		$module = $dirURL[0];
		if (strtolower($module) == 'home') URL::redirect($urlHome);
	}
	if (isset($dirURL[1])){
		$action = $dirURL[1];
		if (strtolower($action) == 'home') URL::redirect(URL::create([$module]));
	}
}
/*
$currentMudule = "{$module}_{$action}";

if (Session::get('SENBAC_LOGGED_DASHBOARD') === true){
	$deny_module = ['accounts_login', 'accounts_register'];
	if (in_array($currentMudule, $deny_module)) URL::redirect($urlHome);
}
else{
	$access_module = ['accounts_login', 'accounts_register', 'Theme_Public'];

	if (!in_array($currentMudule, $access_module)){
		$url = URL::create(['accounts', 'login'], ['return' => $urlCurrent]);
		URL::redirect($url);
	}
}
*/
include_once 'Function.php';

if ($this->configRAU()){
	$module = RAU_string($module);
	$action = RAU_string($action);
}

$fileModel = $module;
if ($module == 'Home' && $action == 'Home') $fileModel = 'Main';
include_once __DIR__ . "/{$module}/{$fileModel}.php";
$model = new $fileModel();

$moduleDir = __DIR__ . "/{$module}/";
if (!File::isDir($moduleDir)) return ;