<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;
use Library\Session;
use Library\Validate;
use Application\Response;

$tokenName = 'accounts_form_token';
$commitName = 'Accounts_Form_Edit';

include_once 'Accounts.php';
$model = new Accounts();

$id = NULL;

if (isset($get['id'])){
    $id = $get['id'];
    $account = $model->getById($id);
    if (!isset($account['_id'])) URL::redirect(URL::create(['admin', 'accounts']));

    if (validate_token_commit($post, $tokenName, $commitName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];

        $rules = [
            'email' => ['type' => 'string', 'min' => 7, 'max' => 100],
            'password' => ['type' => 'string', 'min' => 0, 'max' => 32],
            'firstname' => ['type' => 'string', 'min' => 2, 'max' => 100],
            'lastname' => ['type' => 'string', 'min' => 2, 'max' => 100]
        ];
        $columns = array_keys($rules);

        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();

        if ($validate->isFullValid()){
            $email = $post['email'];
            $response['message'] = 'Email already used.';

            if (!$model->existEmailDiffId($email, $id)){
                $response['message'] = 'Password invalid.';
                $lenpass = strlen($data['password']);

                if ($lenpass == 0 || $lenpass >= 6){
                    if ($lenpass == 0) $data = array_diff_key($data, ['password' => null]);
                    $result = $model->editAccount($data, $id);
                    $response['message'] = 'Connect error. Please try again.';

                    if (isset($result['ok']) && $result['ok'] == 1){
                        $response = [
                            'message' => 'Update account successfully.', 
                            'status' => 'success', 
                            'click' => 1
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
    
    $tpl->merge($account, 'account');
    $tpl->merge(0, 'min_passwd');
}
else{
    $commitName = 'Accounts_Form_Create';

    if (validate_token_commit($post, $tokenName, $commitName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];

        $rules = [
            'email' => ['type' => 'email', 'min' => 7, 'max' => 100],
            'password' => ['type' => 'string', 'min' => 6, 'max' => 100],
            'firstname' => ['type' => 'string', 'min' => 2, 'max' => 100],
            'lastname' => ['type' => 'string', 'min' => 2, 'max' => 100]
        ];
        $columns = array_keys($rules);

        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();

        if ($validate->isFullValid()){
            $email = $post['email'];
            $response['message'] = 'Email already exist.';

            if (!$model->existEmail($email)){
                $result = $model->createAccount($data);
                $response['message'] = 'Connect error. Please try again.';

                if (isset($result['ok']) && $result['ok'] == 1){
                    $user = $model->getByEmail($email);
                    $response = [
                        'message' => 'Create account successfully.',
                        'status' => 'success',
                        'url' => URL::create(['admin', 'accounts'], ['id' => $user['_id']->__toString()])
                    ];
                }
            }
        }

        $this->response = $response;
        new Response('Content-Type: application/json', function(){
            return $this->response;
        });
    }

    $tpl->merge('Create new accounts', 'header_form_name');
    $tpl->merge(6, 'min_passwd');
}

$linit = 10;
$start = 0;
$list = $model->getListAccount($start, $limit);

foreach($list as $acc){
    $accId = $acc['_id']->__toString();
    if ($accId == $id) $acc['class'] = 'active';

    $acc['url'] = URL::create(['admin', 'accounts'], ['id' => $accId]);
    $tpl->assign($acc, 'accounts');
}

$tpl->setFile(['left' => 'accounts.left', 'right' => 'accounts.right']);
$tpl->merge($commitName, 'accounts_form_commit');

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);
$tpl->merge($strToken, $tokenName);