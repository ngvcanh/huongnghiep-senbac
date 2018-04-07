<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\URL;
use Library\Validate;
use Application\Response;

$tpl->merge('Maps', 'title_project');
$tokenName = 'maps_form_token';
$commitName = 'Maps_Form_Commit';

if (validate_token_commit($post, $tokenName, $commitName)){
  $response = ['message' => 'Data invalid.', 'status' => 'error'];
  $url_result_maps = URL::create(['maps', 'result-maps']);

  $rules = [
    'address'           => ['type' => 'string', 'min' => 1, 'max' => 200],
    'street'            => ['type' => 'string', 'min' => 1, 'max' => 200],
    'district_id'       => ['type' => 'int', 'min' => 1],
    'ward_id'           => ['type' => 'int', 'min' => 0],
    'point_match'       => ['type' => 'dble', 'min' => 1, 'max' => 10],
    'point_literature'  => ['type' => 'dble', 'min' => 1, 'max' => 10],
    'point_more'        => ['type' => 'dble', 'min' => 1, 'max' => 10],
    'about_km'          => ['type' => 'int', 'min' => 0, 'max' => 100],
    'about_point'       => ['type' => 'int', 'min' => 0, 'max' => 100],
  ];
  $columns = array_keys($rules);

  $validate = Validate::getInstance($rules, $columns)->setSource($post);
  $data = $validate->run();

  if ($validate->isFullValid()){   

    $result_maps  = [
      'address'           => $post['address'],
      'street'            => $post['street'],
      'ward_id'           => $post['ward_id'],
      'district_id'       => $post['district_id'],
      'point_match'       => $post['point_match'],
      'point_literature'  => $post['point_literature'],
      'point_more'        => $post['point_more'],
      'about_km'          => $post['about_km'],
      'about_point'       => $post['about_point'],
    ];
  
    Session::set('result_maps', $result_maps);
    $response = [
      'message' => 'Vui Lòng Đợi Trong Giây Lát',
      'status'  => 'success', 
      'title'   => 'Thông Báo',
      'url'     => $url_result_maps
    ];
  
    $this->response = $response;
    new Response('Content-Type: application/json', function(){
      return $this->response;
    });
    
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

$url_get_ward = URL::create(['maps', 'get-name-ward']);

$tpl->merge($url_get_ward, 'url_get_ward');
$strToken = $token->generate(32);
Session::add($tokenName, $strToken);

$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'maps_form_commit');

$tpl->setFile([
		'head'        => 'maps/index/head',
    'body'	      => 'maps/index/body',
    'sub_script'  => 'maps/index/sub_script'
]);
