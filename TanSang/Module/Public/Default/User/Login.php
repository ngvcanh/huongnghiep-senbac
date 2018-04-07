<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;
use Library\Validate;
use Application\Response;

$commitName = 'HBSQL_Dashboard_Login';
$tokenName	= 'hbsql_login_token';

if (validate_token_commit($post, $tokenName, $commitName)){
	$response = ['message' => 'Username or Password incorrect.', 'status' => 'error'];

	$rules = [
		'username' => ['type' => 'string', 'min' => 3, 'max' => 20],
		'password' => ['type' => 'string', 'min' => 6, 'max' => 32]
	];
	$columns = array_keys($rules);

	$validate = Validate::getInstance($rules, $columns)->setSource($post);
	$validate->run();

	if ($validate->isFullValid()){
		include_once __DIR__ . '/User.php';
		$User = new User();

		if ($User->login($post['username'], $post['password'])){
			Session::set('HBSQL_Administator_Logged', true);
			Session::set('HBSQL_UserInfo', $User->info);
			Session::remove($tokenName);
			
			$redirect = (isset($get['return']) && is_string($get['return'])) ? $get['return'] : $urlHome;
			$response['url']		= $redirect;
			
			$response['message'] 	= 'Login success.';
			$response['status']		= 'success';
		}
	}

	$this->contentResponse = $response;
	new Response('Content-Type: application/json', function(){
		return $this->contentResponse;
	});
}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);
$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'hbsql_login_commit');

$strToken = $token->generate(32);
Session::add('allow_stylesheet_token', $strToken);

$urlMainCSS = URL::create(['static', 'css', 'theme', 'login+alert'], ['token' => $strToken]);
$tpl->merge($urlMainCSS, 'main_css');

$tpl->setFile([
    'main' => 'login',
	'head' => 'login.head'
]);
$tpl->merge('Signin Dashboard', 'site_title');
$tpl->merge($urlCurrent, 'hbsql_url_main_form');