<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;

$tokenName = 'blog_category_form_token';
$commitName = 'Blog_Category_Create';

if (validate_token_commit($post, $tokenName, $commitName)){

}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);

$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'blog_category_form_commit');

$tpl->setFile([
    'content' 		=> 'blog-category/form',
    'sub_script' 	=> 'blog-category/create-script'
]);

$tpl->assign(['name' => 'Create new'], 'breadcrumb');
$tpl->merge('Blog category manager', 'breadcrumb_name');
$tpl->merge('Create new', 'breadcrumb_action');
$tpl->merge('Create', 'button_form');