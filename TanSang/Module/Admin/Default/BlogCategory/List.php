<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  
  use Library\URL;
  use Library\Session;
  use Library\Validate;
  use Application\Response;
  
  $tokenDeleteName        = 'blog_category_form_token';
  $commitDeleteName       = 'Blog_Category_Form_Commit';

  if (validate_token_commit($post, $tokenDeleteName, $commitDeleteName)){
    $response = ['message' => 'Data invalid.', 'status' => 'error'];
  
    $rules = [
      'id' => ['type' => 'int', 'min' => 1],
    ];
    $columns = array_keys($rules);
  
    $validate = Validate::getInstance($rules, $columns)->setSource($post);
    $data = $validate->run();
  
    if ($validate->isFullValid()){
      $response = ['message' => 'Delete success.', 'status' => 'success'];
      $id = $data['id'];
      $result = $model->deleteById($id);
    } 
  
    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
  
}


  $thisModule = 'blog-category';

  $listCate = $model->getListCategory();

  while($row = $model->fetch($listCate)){
    $row->url_edit = URL::create([K_URL_DASH, $thisModule, 'edit',$row->id]);
    $row->url_delete = URL::create([K_URL_DASH, $thisModule, 'list', $row->id]);
    $tpl->assign($row, 'listBlogCate');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('Blog category manager', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List Categoty', 'breadcrumb_title');

  $strToken = $token->generate(32);
  Session::add($tokenDeleteName, $strToken);

  $tpl->merge($strToken, $tokenDeleteName);
  $tpl->merge($commitDeleteName, 'blog_category_form_commit');

  $tpl->setFile([
    'content' 		=> 'blog-category/list',
    'sub_script' 		=> 'blog-category/list-script',
    'sub_style' 		=> 'blog-category/list-style',
  ]);