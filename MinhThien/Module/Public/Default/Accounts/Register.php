<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
use Library\Session;
use Library\Validate;
use Library\URL;
use Library\Mailer;
use Application\Response;

$token_name = 'accounts_register_token';
$commit_name = 'Accounts_Register_Commit';

if (validate_token_commit($post, $token_name, $commit_name)) {
	$response = ['message' => 'Data invalid.', 'status' => 'error'];

	$rules = [
		'email' => ['type' => 'email', 'min' => 7, 'max' => 100],
		'password' => ['type' => 'string', 'min' => 6, 'max' => 32],
		'confirm_password' => ['type' => 'string', 'min' => 6, 'max' => 32],
		'firstname' => ['type' => 'string', 'min' => 1, 'max' => 100],
		'lastname' => ['type' => 'string', 'min' => 1, 'max' => 100]
	];
	$columns = array_keys($rules);

	$validate = Validate::getInstance($rules, $columns)->setSource($post);
	$data = $validate->run();

	if ($validate->isFullValid()){
        $response['message'] = 'Confirm Password incorrect.';

        if ($model->confirmPassword($data['password'], $data['confirm_password'])) {
			$response['message'] = 'Email already used.';
			$email = $data['email'];
			
			if(!$model->existEmail($email)){
				$tokenActive = $token->generate(32);
				$data['token_active'] = $tokenActive;
				
				$result = $model->createAccount($data);
				$response['message'] = 'Connect Failed. Please try again.';

				if ($model->isDBResult($result) && $result->getInsertedCount() === 1){
					//Send Mail
					new Mailer(
						'Register success. Please active email to continue.',
						'You register success account at http://crm.senbac.com site. Please active email to continue.',
						['ngvcanh2014@gmail.com', 'Mr Ken'],
						[['ngvcanh2015@gmail.com']],
						['host' => 'smtp.gmail.com', 'user' => 'ngvcanh2014@gmail.com', 'pass' => 'canhcanh']
					);

					$myAccount = $model->getByEmail($email);
					Session::set(SESSION_LOGIN_KEY, true);
					Session::set(SESSION_USER_KEY, $myAccount->_id);

					$response = [
						'message' => 'Register Success. Please active email.',
						'status' => 'success',
						'url' => $urlHome
					];
				}
			}
        }
	}

	$this->response = $response;
	new Response('Content-Type: application/json', function(){
		return $this->response;
	});	
}



$strToken = $token->generate(32);
Session::add($token_name, $strToken);
$tpl->merge($strToken, $token_name);

$tpl->merge($commit_name, 'accounts_register_commit');

$tpl->setFile([
    'main' => 'accounts/register',
	'head' => 'accounts/head'
]);