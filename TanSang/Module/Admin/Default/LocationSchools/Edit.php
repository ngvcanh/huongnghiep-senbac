<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Session;
use Library\Validate;
use Library\URL;
use Application\Response;

$tokenName = 'location_schools_form_token';
$commitName = 'Location_Schools_edit';

if (isset($dirURL[2]) && is_numeric($dirURL[2])){

  $id = $dirURL[2];
  $school = $model->getById($id);
  if(isset($school->id)){
    
    if (validate_token_commit($post, $tokenName, $commitName)){
      $response = ['message' => 'Data invalid.', 'status' => 'error'];

      $rules = [
        'name' => ['type' => 'string', 'min' => 1, 'max' => 200],
        'point' => ['type' => 'dble', 'min' => 1, 'max' => 100],
        'district_id' => ['type' => 'int', 'min' => 1],
        'ward_id' => ['type' => 'int', 'min' => 1],
        'street' => ['type' => 'string', 'min' => 0, 'max' => 200],
        'address' => ['type' => 'string', 'min' => 0, 'max' => 200]
      ];
      $columns = array_keys($rules);

      $validate = Validate::getInstance($rules, $columns)->setSource($post);
      $data = $validate->run();

      if ($validate->isFullValid()){     
        $response['message'] = 'Can not update School info';
        $data['id'] = $school->id;
        $result =  $model->updateSchool($data);
        if($result){
          $response = ['message' => 'Update success', 'status' => 'success'];
        }
        
      }
      
      $this->response = $response;
      new Response('Content-Type: application/json', function(){
        return $this->response;
      });
    
    }

    $listDistrict = $model->getListDistrict();

    while($rowDistrict = $model->fetch($listDistrict)){
      if($rowDistrict->id == $school->district_id) $rowDistrict->class = 'active';
      $tpl->assign($rowDistrict, 'listDistrict');
    }

    $listWard = $model->getListWard(['district_id' => $school->district_id]);

    while($rowWard = $model->fetch($listWard)){
      if($rowWard->id == $school->ward_id) $rowWard->class = 'active';
      $tpl->assign($rowWard, 'listWard');
    }
    
    $strToken = $token->generate(32);
    Session::add($tokenName, $strToken);
  
    $tpl->merge($strToken, $tokenName);
    $tpl->merge($commitName, 'location_schools_form_commit');
  
    $url_get_street =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-street']);
    $url_get_ward =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-ward']);

    $tpl->merge($url_get_street, 'url_get_street');
    $tpl->merge($url_get_ward, 'url_get_ward');
    $tpl->merge($school, 'dataSchools');
    $tpl->setFile([
      'content' 		  => $thisModule . '/form',
      'sub_script' 		=> $thisModule . '/create-script',
      'sub_style' 		=> $thisModule . '/create-style',
    ]);
  
    $tpl->assign(['name' => 'Edit'], 'breadcrumb');
    $tpl->merge('Location Schools Manager', 'breadcrumb_name');
    $tpl->merge('Edit', 'breadcrumb_action');
    $tpl->merge('Edit', 'button_form');
  }
  

  
}
