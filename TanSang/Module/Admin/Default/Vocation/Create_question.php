<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;
use Library\Validate;
use Library\Session;
use Application\Response;

$tokenName = 'vocation_question_form_token';
$commitName = 'Vocation_Question_Create';

$listVocationGroup = $model->getListVocationGroup();

while($row = $model->fetch($listVocationGroup)){
    $tpl->assign($row, 'listVocationGroup');
}

if (validate_token_commit($post, $tokenName, $commitName)){
  $response = ['message' => 'Data invalid.', 'status' => 'error'];
  $rules = [
    'group_id' => ['type' => 'int', 'min' => 1],
    'question' => ['type' => 'str', 'min' =>0],
    'point'    => ['type' => 'dble', 'min' => 1],
    'ordering' => ['type' => 'int', 'min' => 0],
  ];
  $columns = array_keys($rules);

  $validate = Validate::getInstance($rules, $columns)->setSource($post);
  $data = $validate->run();

  if ($validate->isFullValid()){
    $response['message'] = 'Can not create question.';
    $result = $model->createQuestion($data);
    
    if($result > 0){
        $response = ['message' => 'Create success.', 'status' => 'success'];
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
$tpl->merge($commitName, 'vocation_question_form_commit');

$url_list = URL::create([K_URL_DASH, $thisModule, 'question']);
$tpl->merge($url_list, 'url_list');
$tpl->setFile([
    'content' 		=> 'vocation/question/form',
    'sub_script' 	=> 'vocation/question/script'
]);

$tpl->assign(['name' => 'Create new'], 'breadcrumb');
$tpl->merge('Question vocation manager', 'breadcrumb_name');
$tpl->merge('Create new', 'breadcrumb_action');
$tpl->merge('Create', 'button_form');