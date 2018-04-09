<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if ($action == 'Home') URL::redirect(URL::create(['contacts', 'list']));

$access_action = ['nodejs'];
if (!in_array($action, $access_action)) return;

$actionPath = $moduleDir . $action . '.php';
if (!File::isFile($actionPath)) return;
include_once $actionPath;