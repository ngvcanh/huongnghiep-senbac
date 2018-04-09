<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

  use Library\URL;
  use Library\Validate;
  use Library\Session;
  use Application\Response;

  $tokenName        = 'district_create_token';
  $commitName       = 'District_Create_Commit';

  $tokenEditName    = 'district_form_edit_token';
  $commitEditName   = 'District_Edit_Commit';

  $tokenDeleteName  = 'district_detele_token';
  $commitDeleteName = 'District_Delete_Commit';

  if (validate_token_commit($post, $tokenName, $commitName)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];

    $rules = [
      'name' => ['type' => 'string', 'min' => 1, 'max' => 200]
    ];
    $columns = array_keys($rules);

    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();

    if ($validate->isFullValid()){
        $response['message'] = 'Can not create Group.';
        $result = $model->createDistrict($data);
        if($result > 0){
            $response = ['message' => 'Create success.', 'status' => 'success'];
        }
    } 

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
  }

  if(validate_token_commit($post, $tokenDeleteName, $commitDeleteName)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];
    $rules = ['id' => ['type' => 'int', 'min' => 1]];
    $columns = array_keys($rules);

    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();

    if ($validate->isFullValid()){
        $id = $data['id'];
        $model->deleteDistrictById($id);
        $response = ['message' => 'Delele success.', 'status' => 'success'];        
    } 

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
}

  if(validate_token_commit($post, $tokenEditName, $commitEditName)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];
    $rules = [
      'id' => ['type' => 'int', 'min' => 1],
      'name' => ['type' => 'string', 'min' => 1, 'max' => 200]
    ];
    $columns = array_keys($rules);

    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();

    if ($validate->isFullValid()){
        $response['message'] = 'Can not found id.';
        $id = $data['id'];
        $dataGroup = $model->getById($id);

        if($dataGroup->id){
            $result = $model->updateDistrictById($id, $data);
            $response = ['message' => 'Update success.', 'status' => 'success', 'click' => 1];
        }         
    } 

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
  }

  $listDistrict = $model->getListDistrict();

  while($row = $model->fetch($listDistrict)){
    $row->url_edit = URL::create([K_URL_DASH, $thisModule, 'list'], ['id' => $row->id]);
    $tpl->assign($row, 'listDistrict');
  }

  //token and commit add
  $strToken = $token->generate(32);
  Session::add($tokenName, $strToken);

  $tpl->merge($strToken, $tokenName);
  $tpl->merge($commitName, 'district_create_commit');

  //token and commit edit
  $strToken = $token->generate(32);
  Session::add($tokenEditName, $strToken);

  $tpl->merge($strToken, $tokenEditName);
  $tpl->merge($commitEditName, 'district_edit_commit');

  //token and commit delete
  $strToken = $token->generate(32);
  Session::add($tokenDeleteName, $strToken);

  $tpl->merge($strToken, $tokenDeleteName);
  $tpl->merge($commitDeleteName, 'district_delete_commit');

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('District List', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List District', 'breadcrumb_title');

  $tpl->merge('Create District', 'form-name');
  $tpl->merge('Create', 'button_form');
  $tpl->setFile([
    'content' 		  => $thisModule . '/list',
    'sub_script' 		=> $thisModule . '/list-script',
    'sub_style' 		=> $thisModule . '/list-style',
  ]);