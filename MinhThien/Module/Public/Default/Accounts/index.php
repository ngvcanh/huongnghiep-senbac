<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;

$access_action = ['Active', 'Login', 'Register', 'Logout'];
if (!in_array($action, $access_action)) return;

$actionPath = $moduleDir . $action . '.php';
if (!File::isFile($actionPath)) return;
include_once $actionPath;