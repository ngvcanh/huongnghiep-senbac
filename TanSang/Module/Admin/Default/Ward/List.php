<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

  use Library\URL;
  use Library\Validate;
  use Library\Session;
  use Application\Response;

  $tokenName        = 'ward_create_token';
  $commitName       = 'Ward_Create_Commit';

  $tokenEditName    = 'ward_form_edit_token';
  $commitEditName   = 'Ward_Edit_Commit';

  $tokenDeleteName  = 'ward_detele_token';
  $commitDeleteName = 'Ward_Delete_Commit';

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
        $result = $model->createWard($data);
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
        $model->deleteWardById($id);
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
        $response['message'] = 'Can not found.';
        $id = $data['id'];
        $dataGroup = $model->getWardById($id);

        if($dataGroup->id){
            $result = $model->updateWardById($id, $data);
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
    $tpl->assign($row, 'listDistrict');
  }

  $listWard = $model->getListWard();

  while($row = $model->fetch($listWard)){
    $tpl->merge('selected', "ward_{$row->id}_{$row->district_id}");
    $tpl->assign($row, 'listWard');

    $url_edit_ward       = URL::create([K_URL_DASH, $thisModule, 'edit', $row->id]);
    $url_delete_ward    = URL::create([K_URL_DASH, $thisModule, 'delete', $row->id]);

    $tpl->assign([
      'edit'    => $url_edit_ward,
      'delete'  => $url_delete_ward
    ], 
    'listWard.url_ward');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('Ward', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List Ward', 'breadcrumb_title');
  $tpl->merge('Create Ward', 'form_name');
  $tpl->merge('Create', 'button_form');
   //token and commit add
  $strToken = $token->generate(32);
  Session::add($tokenName, $strToken);

  $tpl->merge($strToken, $tokenName);
  $tpl->merge($commitName, 'ward_create_commit');

  //token and commit edit
  $strToken = $token->generate(32);
  Session::add($tokenEditName, $strToken);

  $tpl->merge($strToken, $tokenEditName);
  $tpl->merge($commitEditName, 'ward_edit_commit');

  //token and commit delete
  $strToken = $token->generate(32);
  Session::add($tokenDeleteName, $strToken);

  $tpl->merge($strToken, $tokenDeleteName);
  $tpl->merge($commitDeleteName, 'ward_delete_commit');

  $tpl->setFile([
    'content' 		  => $thisModule . '/list',
    'sub_script' 		=> $thisModule . '/list-script',
    'sub_style' 		=> $thisModule . '/list-style',
  ]);