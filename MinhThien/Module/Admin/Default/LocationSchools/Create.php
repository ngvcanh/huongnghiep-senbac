<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;

$tokenName = 'location_schools_form_token';
$commitName = 'Location_Schools_Create';

if (validate_token_commit($post, $tokenName, $commitName)){

}

$listDistrict = $model->getListDistrict();
while($rowDistrict = $model->fetch($listDistrict)){
  $tpl->assign($rowDistrict, 'listDistrict');
}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);

$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'blog_category_form_commit');

$url_get_street =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-street']);
$url_get_ward =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-ward']);
$tpl->merge($url_get_street, 'url_get_street');
$tpl->merge($url_get_ward, 'url_get_ward');

$tpl->setFile([
  'content' 		  => $thisModule . '/form',
  'sub_script' 		=> $thisModule . '/create-script',
  'sub_style' 		=> $thisModule . '/create-style',
]);

$tpl->assign(['name' => 'Create new'], 'breadcrumb');
$tpl->merge('Location Schools Manager', 'breadcrumb_name');
$tpl->merge('Create new', 'breadcrumb_action');
$tpl->merge('Create', 'button_form');