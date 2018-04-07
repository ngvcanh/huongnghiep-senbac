<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Token;
use Library\File;
use Library\URL;

date_default_timezone_set('Asia/Ho_Chi_Minh');
$token      = Token::getInstance();

$tpl = $this->template;
$tpl->merge($urlCurrent, 'url_current');
$tpl->merge($urlHome, 'url_home');
$tpl->merge($urlProject, 'url_project');

$tpl->setFile([
    'head' => 'head',
    'body' => 'body',
    'menu' => 'menu',
    'sidebar' => 'sidebar',
    'wrapper' => 'wrapper',
    'breadcrumb' => 'breadcrumb',
    'footer' => 'footer',
    'script' => 'script'
]);

$urlSidebar = [
    'district'     => [K_URL_DASH, 'district', 'list'],
    'ward'         => [K_URL_DASH, 'ward', 'list'],
    'street'       => [K_URL_DASH, 'street', 'list'],
    'blog_cate'    => [K_URL_DASH, 'blog-category', 'list'],
];

foreach($urlSidebar as $key => $sidebar){
    $tpl->merge(URL::create($sidebar), 'url_' . $key);
};

$indexPath = $moduleDir . 'index.php';
if (!File::isFile($indexPath)) return;
include_once $indexPath;