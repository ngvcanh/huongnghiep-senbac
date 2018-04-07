<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if (!isset($dirURL[1])) $action = 'Group';
else if ($dirURL[1] == 'Group') $action = 'unknown';

$access_action = ['Create_question', 'Group', 'Edit_question', 'Question'];
if (!in_array($action, $access_action)) return ;

$thisModule = 'vocation';
$urlList = URL::create([K_URL_DASH, $thisModule, 'group']);

$tpl->assign(['url' => $urlList, 'name' => 'Vocation'], 'breadcrumb');

$actionPath = __DIR__ . "/{$action}.php";

$actionPath;
if (!File::isFile($actionPath)) return ;
include_once $actionPath;