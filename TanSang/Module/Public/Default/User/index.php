<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;

$access_action = ['Login', 'Logout'];
if (!in_array($action, $access_action)) die(pathinfo(__FILE__, PATHINFO_FILENAME));

$actionPath = $moduleDir . $action . '.php';
if (!File::isFile($actionPath)) die(pathinfo(__FILE__, PATHINFO_FILENAME));
include_once $actionPath;