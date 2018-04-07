<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;
use Library\Validate;
use Library\Session;
use Application\Response;

$tokenName = 'blog_category_form_token';
$commitName = 'Blog_Category_Create';

if (validate_token_commit($post, $tokenName, $commitName)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];
    
    $rules = [ 
      'name' => ['type' => 'string', 'min' => 1, 'max' => 200]
    ];
    $columns = array_keys($rules);

    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();

    if ($validate->isFullValid()){

    }

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
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