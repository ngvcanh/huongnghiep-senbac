<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if (!isset($dirURL[1])) $action = 'List';
else if ($dirURL[1] == 'List') $action = 'unknown';

$access_action = ['AjaxGetStreet', 'AjaxGetWard', 'Create', 'Edit', 'List', 'Delete'];
if (!in_array($action, $access_action)) return ;

$thisModule = 'location-schools';
$urlList = URL::create([K_URL_DASH, $thisModule, 'list']);
$tpl->merge(URL::create([K_URL_DASH, $thisModule, 'create']), 'url_add');
$tpl->merge($urlList, 'url_list');
$tpl->assign(['url' => $urlList, 'name' => 'Location Schools'], 'breadcrumb');

$actionPath = __DIR__ . "/{$action}.php";

$actionPath;
if (!File::isFile($actionPath)) return ;
include_once $actionPath;