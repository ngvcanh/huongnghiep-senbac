<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;

$access_action = ['Home'];
if (!in_array($action, $access_action)) return ;

$tpl->merge('active', 'das_active');
$actionPath = __DIR__ . "/{$action}.php";
if (!File::isFile($actionPath)) return ;

include_once $actionPath;