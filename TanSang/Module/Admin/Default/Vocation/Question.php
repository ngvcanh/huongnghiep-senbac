<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

    use Library\URL;
    use Library\Validate;
    use Library\Session;
    use Application\Response;
    
    $tokenDeleteName = 'vocation_question_detele_token';
    $commitDeleteName = 'Vocation_Question_Delete_Commit';

    if (validate_token_commit($post, $tokenDeleteName, $commitDeleteName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];
      
        $rules = [
          'id' => ['type' => 'int', 'min' => 1],
        ];
        $columns = array_keys($rules);
      
        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();
      
        if ($validate->isFullValid()){
          $id = 0;
      
          if (isset($dirURL[2]) && is_numeric($dirURL[2])){
      
            $id = $dirURL[2];
          }
          $response = ['message' => 'Delete success.', 'status' => 'success'];
          $result = $model->deleteQuestionById($id);
        } 
      
        $this->response = $response;
        new Response('Content-Type: application/json', function(){
            return $this->response;
        });
      
    }

    $listQuestion = $model->getListVocationQuestion();
    $thisModule = 'vocation';

    while($row = $model->fetch($listQuestion)){
        $row->url_edit = URL::create([K_URL_DASH, $thisModule, 'edit_question', $row->id]);
        $row->url_delete = URL::create([K_URL_DASH, $thisModule, 'question', $row->id]);
        $tpl->assign($row, 'listVocationQuestion');
    }

    $tpl->assign(['name' => 'Question'], 'breadcrumb');
    $tpl->merge('Vocation', 'breadcrumb_name');
    $tpl->merge('Question', 'breadcrumb_action');
    $tpl->merge('Question vocation', 'breadcrumb_title');

    $url_add=URL::create([K_URL_DASH, $thisModule, 'create_question']);
    $tpl->merge($url_add, 'url_add');

    $strToken = $token->generate(32);
    Session::add($tokenDeleteName, $strToken);

    $tpl->merge($strToken, $tokenDeleteName);
    $tpl->merge($commitDeleteName, 'vocation_question_delete_commit');

    $tpl->setFile([
        'content'         => 'vocation/question/list',
        'sub_script'      => 'vocation/question/script',
        'sub_style' 	  => 'vocation/question/css',
    ]);
    






  

