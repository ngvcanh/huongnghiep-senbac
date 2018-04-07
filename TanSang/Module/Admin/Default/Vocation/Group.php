<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

    use Library\URL;
    use Library\Validate;
    use Library\Session;
    use Application\Response;

    $flag = true;
    $group = new stdClass;

    $tokenName = 'vocation_group_create_token';
    $commitName = 'Vocation_Group_Create';

    $tokenEditName = 'vocation_group_form_edit_token';
    $commitEditName = 'Vocation_Group_Edit';

    $tokenDeleteName = 'vocation_group_detele_token';
    $commitDeleteName = 'Vocation_Group_Delete';

    $tpl->merge('Create group', 'form_name');
    $tpl->merge('Create', 'button_form');

    if (validate_token_commit($post, $tokenName, $commitName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];

        $rules = [
            'name' => ['type' => 'string', 'min' => 1, 'max' => 200],
            'ordering' => ['type' => 'int', 'min' => 0, 'max' => 9],
        ];
        $columns = array_keys($rules);
    
        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();
    
        if ($validate->isFullValid()){
            $response['message'] = 'Can not create Group.';
            $result = $model->createGroup($data);
            if($result > 0){
                $response = ['message' => 'Create success.', 'status' => 'success'];
            }
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
            'name' => ['type' => 'string', 'min' => 1, 'max' => 200],
            'ordering' => ['type' => 'int', 'min' => 0, 'max' => 9],
        ];
        $columns = array_keys($rules);
    
        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();
    
        if ($validate->isFullValid()){
            $response['message'] = 'Can not found.';

            $id = $data['id'];
            $dataGroup = $model->getGroup($id);

            if($dataGroup->id){
                $result = $model->updateGroupById($dataGroup->id, $data);
                $response = ['message' => 'Update success.', 'status' => 'success', 'click' => 1];
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
            $model->deleteGroupById($id);
            $response = ['message' => 'Delele success.', 'status' => 'success'];        
        } 

        $this->response = $response;
        new Response('Content-Type: application/json', function(){
            return $this->response;
        });
    }

    $listGroups = $model->getListVocationGroup();

        while($row = $model->fetch($listGroups)){
            $row->url_edit = URL::create([K_URL_DASH, $thisModule, 'group'], ['id' => $row->id]);
            $tpl->assign($row, 'listVocationGroups');
    
    }

    $tpl->assign(['name' => 'Groups'], 'breadcrumb');
    $tpl->merge('Vocation', 'breadcrumb_name');
    $tpl->merge('Groups', 'breadcrumb_action');
    $tpl->merge('Group vocation', 'breadcrumb_title');
    
    //token and commit create
    $strToken = $token->generate(32);
    Session::add($tokenName, $strToken);

    $tpl->merge($strToken, $tokenName);
    $tpl->merge($commitName, 'vocation_create_commit');

    //token and commit edit
    $strToken = $token->generate(32);
    Session::add($tokenEditName, $strToken);

    $tpl->merge($strToken, $tokenEditName);
    $tpl->merge($commitEditName, 'vocation_edit_commit');

    //token and commit delete
    $strToken = $token->generate(32);
    Session::add($tokenDeleteName, $strToken);

    $tpl->merge($strToken, $tokenDeleteName);
    $tpl->merge($commitDeleteName, 'vocation_delete_commit');

    $tpl->setFile([
        'content'         => 'vocation/group',
        'sub_script'      => 'vocation/script'
    ]);
  

