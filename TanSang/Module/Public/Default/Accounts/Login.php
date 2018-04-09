<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;
use Library\Validate;
use Application\Response;

$commitName = 'Accounts_Login';
$tokenName	= 'accounts_login_token';

if (validate_token_commit($post, $tokenName, $commitName)){
	$response = ['message' => 'Data invalid.', 'status' => 'error'];

	$rules = [
		'email' => ['type' => 'email', 'min' => 7, 'max' => 100],
		'password' => ['type' => 'string', 'min' => 6, 'max' => 32]
	];
	$columns = array_keys($rules);

	$validate = Validate::getInstance($rules, $columns)->setSource($post);
	$validate->run();

	if ($validate->isFullValid()){
        $response['message'] = 'Username or Password incorrect.';

		if ($model->login($post['email'], $post['password'])){
			Session::set(SESSION_LOGIN_KEY, true);
			Session::set(SESSION_USER_KEY, $model->userid);
			Session::remove($tokenName);

            $redirect = (isset($get['return']) && is_string($get['return'])) ? $get['return'] : $urlHome;
            $response = ['message' => 'Login success.', 'status' => 'success', 'url' => $redirect];
		}
	}

	$this->response = $response;
	new Response('Content-Type: application/json', function(){
		return $this->response;
	});
}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);
$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'accounts_login_commit');

$tpl->setFile([
    'main' => 'accounts/login',
	'head' => 'accounts/head'
]);
$tpl->merge($urlCurrent, 'url_main_form');

$urlRegister = URL::create(['accounts', 'register']);
$tpl->merge($urlRegister, 'url_register');