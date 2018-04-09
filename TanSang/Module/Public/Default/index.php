<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\Session;
use Library\URL;

Session::init();

define('SESSION_LOGIN_KEY', 'SENBAC_LOGGED_DASHBOARD');
define('SESSION_USER_KEY', 'SENBAC_LOGGED_USER_ID');

$module = 'Home';
$action = 'Home';

$urlHome 	= URL::create();
$urlCurrent = URL::getCurrent();

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

$currentMudule = "{$module}_{$action}";

include_once 'Function.php';
include_once 'PublicModel.php';

if ($this->configRAU()){
	$module = RAU_string($module);
	$action = RAU_string($action);
}

include_once "{$module}/{$module}.php";
$model = new $module();

if (Session::get(SESSION_LOGIN_KEY) === true){
	$deny_module = ['accounts_login', 'accounts_register'];
	if (in_array($currentMudule, $deny_module)) URL::redirect($urlHome);
	if ($module == 'Home') URL::redirect(URL::create(['sales']));
}
else{
	$access_module = ['accounts_login', 'accounts_register', 'Theme_Public'];
	if (!in_array($currentMudule, $access_module)){
		$url = URL::create(['accounts', 'login'], ['return' => $urlCurrent]);
		URL::redirect($url);
	}
}

$moduleDir = __DIR__ . "/{$module}/";
if (!File::isDir($moduleDir)) return ;