<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if (!isset($dirURL[1])) $action = 'Home';

$access_action = ['Home', 'ResultMaps', 'GetNameWard', 'ResultSchools'];
if (!in_array($action, $access_action)) return ;

$actionPath = __DIR__ . "/{$action}.php";

if (!File::isFile($actionPath)) return ;

include_once $actionPath;
