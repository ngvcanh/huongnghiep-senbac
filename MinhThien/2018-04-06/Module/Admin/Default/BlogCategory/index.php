<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\URL;

if (!isset($dirURL[1])) $action = 'List';
else if ($dirURL[1] == 'List') $action = 'unknown';

$access_action = ['Create', 'Edit', 'List'];
if (!in_array($action, $access_action)) return ;

$thisModule = 'blog-category';
$urlList = URL::create([K_URL_DASH, $thisModule, 'list']);
$tpl->merge(URL::create([K_URL_DASH, $thisModule, 'create']), 'url_add');
$tpl->merge($urlList, 'url_list');
$tpl->merge('active', 'active_cate');
$tpl->merge('active', 'active_blog_cate');
$tpl->assign(['url' => $urlList, 'name' => 'Blog category'], 'breadcrumb');

$actionPath = __DIR__ . "/{$action}.php";
if (!File::isFile($actionPath)) return ;
include_once $actionPath;