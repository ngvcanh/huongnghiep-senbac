<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Token;
use Library\File;

date_default_timezone_set('Asia/Ho_Chi_Minh');
$token      = Token::getInstance();
$siteTitle  = 'dashboard_title';

$tpl = $this->template;
$tpl->merge($urlCurrent, 'url_current');
$tpl->merge($urlHome, 'url_home');

$tpl->setFile([
    'head' => 'head',
    'main' => 'main',
    'menu' => 'menu',
    'sidebar' => 'sidebar',
    'breadcrumb' => 'breadcrumb'
]);

$myAccount = $model->getMyAccount();
if (isset($myAccount->_id)){
    $myAccount->id = $myAccount->_id->__toString();
    $tpl->setFile(['script' => 'script']);
}
$tpl->merge($myAccount, 'myaccount');

$indexPath = $moduleDir . 'index.php';
if (!File::isFile($indexPath)) return;
include_once $indexPath;