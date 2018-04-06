<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;
use Library\Validate;
use Application\Response;

$tokenName = 'location_schools_form_token';
$commitName = 'Location_Schools_Create';

if (validate_token_commit($post, $tokenName, $commitName)){
  $response = ['message' => 'Data invalid.', 'status' => 'error'];

  $rules = [
    'name'          => ['type' => 'string', 'min' => 1, 'max' => 200],
    'point'         => ['type' => 'dble', 'min' => 1, 'max' => 100],
    'district_id'   => ['type' => 'int', 'min' => 1],
    'ward_id'       => ['type' => 'int', 'min' => 0],
    'street'        => ['type' => 'string', 'min' => 0, 'max' => 200],
    'address'       => ['type' => 'string', 'min' => 0, 'max' => 200],
    'lat'           => ['type' => 'string', 'min' => 1, 'max' => 20],
    'lng'           => ['type' => 'string', 'min' => 1, 'max' => 20]
  ];
  $columns = array_keys($rules);

  $validate = Validate::getInstance($rules, $columns)->setSource($post);
  $data = $validate->run();

  if ($validate->isFullValid()){   

    $response['message'] = 'Can not create School info';
    $result =  $model->createSchool($data);

    if($result > 0){
      $url_edit = URL::create([K_URL_DASH, $thisModule, 'edit', $result]);
      $response = ['message' => 'Update success', 'status' => 'success', 'url' => $url_edit];
    }
    
  }
  
  $this->response = $response;
  new Response('Content-Type: application/json', function(){
    return $this->response;
  });
}

$listDistrict = $model->getListDistrict();
while($rowDistrict = $model->fetch($listDistrict)){
  $tpl->assign($rowDistrict, 'listDistrict');
}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);

$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'location_schools_form_commit');

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