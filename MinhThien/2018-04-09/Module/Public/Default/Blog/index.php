<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

$access_action = ['Cate', 'Detail'];
if (!in_array($action, $access_action)) return ;

$actionPath = __DIR__ . "/{$action}.php";

if (!File::isFile($actionPath)) return ;

include_once $actionPath;