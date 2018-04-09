<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
use Library\URL;
use Library\Session;
use Library\Validate;
use Application\Response;

$tpl->merge('Page Not Fount1', 'site_title');
$tpl->setFile([
    'content' => 'error/404'
]);

$tpl->assign(['name' => 'Not found'], 'breadcrumb');
$tpl->merge('Error page', 'breadcrumb_name');
$tpl->merge('Page not found', 'breadcrumb_action');

if (isset($dirURL[2]) && is_numeric($dirURL[2])){
    $id = $dirURL[2];
    $locationSchools = $model->getLocationById($id);

    if (isset($locationSchools->id)){

      $tpl->merge($id, 'schools_id');
      $tokenName = 'location_schools_form_token';
      $commitName = 'Location_Schools_Edit';

      if (validate_token_commit($post, $tokenName, $commitName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];
  
        $rules = [
          'name'        => ['type' => 'string', 'min' => 1, 'max' => 200],
          'point'       => ['type' => 'dble', 'min' => 1, 'max' => 100],
          'district_id' => ['type' => 'int', 'min' => 1],
          'ward_id'     => ['type' => 'int', 'min' => 0],
          'street'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'address'     => ['type' => 'string', 'min' => 0, 'max' => 200],
          'lat'         => ['type' => 'string', 'min' => 1, 'max' => 20],
          'lng'         => ['type' => 'string', 'min' => 1, 'max' => 20],
          'schools_id'  => ['type' => 'int', 'in' => [$id]]
        ];
        $columns = array_keys($rules);
        
        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();
        if ($validate->isFullValid()){     
          $response['message'] = 'Can not update School info';
          $result =  $model->updateSchoolByID($data, $id);
          if($result >= 0){
            $response = ['message' => 'Update success', 'status' => 'success'];
          }
        }
        
        $this->response = $response;
        new Response('Content-Type: application/json', function(){
          return $this->response;
        });
      
      }

      $tpl->merge($locationSchools, 'locationSchools');

      $listDistrict = $model->getListDistrict();
      while($rowDistrict = $model->fetch($listDistrict)){
        if($rowDistrict->id == $locationSchools->district_id) $rowDistrict->selected = 'selected="selected"';
        $tpl->assign($rowDistrict, 'listDistrict');
      }

      $listWard = $model->getListWard(['district_id' => $locationSchools->district_id]);
      while($rowWard = $model->fetch($listWard)){
        if($rowWard->id == $locationSchools->ward_id) $rowWard->selected = 'selected="selected"';
        $tpl->assign($rowWard, 'listWard');
      }

      $url_get_street =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-street']);
      $url_get_ward =  URL::create([K_URL_DASH, $thisModule, 'ajax-get-ward']);
      $tpl->merge($url_get_street, 'url_get_street');
      $tpl->merge($url_get_ward, 'url_get_ward');
      
      $strToken = $token->generate(32);
      Session::add($tokenName, $strToken);

      $tpl->merge($strToken, $tokenName);
      $tpl->merge($commitName, 'location_schools_form_commit');

      $tpl->setFile([
        'content' 		  => $thisModule . '/form',
        'sub_script' 		=> $thisModule . '/create-script',
        'sub_style' 		=> $thisModule . '/create-style',
      ]);

      $tpl->assign(['name' => 'Edit'], 'breadcrumb');
      $tpl->merge('Location Schools', 'breadcrumb_name');
      $tpl->merge('Edit', 'breadcrumb_action');
      $tpl->merge('Update', 'button_form');
    }
}
