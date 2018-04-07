<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
use Library\URL;
use Library\Session;
use Library\Validate;
use Library\File;
use Application\Response;

$tpl->merge('Page Not Fount', 'site_title');
$tpl->setFile([
    'content' => 'error/404'
]);

$tpl->assign(['name' => 'Not found'], 'breadcrumb');
$tpl->merge('Error page', 'breadcrumb_name');
$tpl->merge('Page not found', 'breadcrumb_action');

if (isset($dirURL[2]) && is_numeric($dirURL[2])){
    $id = $dirURL[2];
    $blogCategory = $model->getCategoryById($id);

    if (isset($blogCategory->id)){

      $tokenName = 'blog_category_form_token';
      $commitName = 'Blog_Category_Edit';

      if (validate_token_commit($post, $tokenName, $commitName)){
        $response = ['message' => 'Data invalid.', 'status' => 'error'];
  
        $rules = [ 
          'name'        => ['type' => 'string', 'min' => 1, 'max' => 200],
          'slug'        => ['type' => 'string', 'min' => 0, 'max' => 200],
          'description' => ['type' => 'string', 'min' => 0, 'max' => 500],
          'ordering'    => ['type' => 'int', 'min' => 0],
          'parent'      => ['type' => 'int', 'min' => 0],
          'title'       => ['type' => 'string', 'min' => 0, 'max' => 200],
          'keywords'    => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_desc'    => ['type' => 'string', 'min' => 0, 'max' => 500],
          'seo_h1'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_h2'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_h3'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_h4'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_h5'      => ['type' => 'string', 'min' => 0, 'max' => 200],
          'seo_h6'      => ['type' => 'string', 'min' => 0, 'max' => 200]
        ];
        $columns = array_keys($rules);
        
        $validate = Validate::getInstance($rules, $columns)->setSource($post);
        $data = $validate->run();

        if ($validate->isFullValid()){     
          $result = 1 ;
          if(isset($files['image'])){
            $response['message'] = 'Can not upload Image.';
            $result = 0 ;

            $image = $files['image']['tmp_name'][0];
            $path = $files['image']['name'][0];
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $dir = 'Data\Uploads\Images';
            if(!File::isDir( $dir)) File::mkdir($dir);

            if(File::isFile($image)){
                do{
                    $filename = $token->generate(32);
                    $fileSave = $dir . '\\' . $filename . '.' . $ext;
                }while (File::exist($fileSave));

                $data['image'] =  $fileSave;
                $result = copy($image,$fileSave);
            }
          }
          if($result == 1){
            $model->updateById($id, $data);
            $response = ['message' => 'Update success', 'status' => 'success'];
          }
        }
        
        $this->response = $response;
        new Response('Content-Type: application/json', function(){
          return $this->response;
        });      
      }

      $tpl->merge($blogCategory, 'blogCategory');

      $listCate = $model->getListCategory(['parent' => 0]);

      while($row = $model->fetch($listCate)){

        if($row->id == $blogCategory->parent) $row->classes = 'selected'; 
        $tpl->assign($row, 'listBlogCate');
        $listSubCate = $model->getListCategory(['parent' => $row->id]);

        while($row1 = $model->fetch($listSubCate)){ 

            if($row1->id == $blogCategory->parent) $row1->classes = 'selected'; 
            $tpl->assign($row1, 'listBlogCate.listSubCate');    
        }
      }

      $strToken = $token->generate(32);
      Session::add($tokenName, $strToken);

      $tpl->merge($strToken, $tokenName);
      $tpl->merge($commitName, 'blog_category_form_commit');

      $tpl->setFile([
        'content' 		  => $thisModule . '/form',
        'sub_script' 		=> $thisModule . '/create-script'
      ]);

      $tpl->assign(['name' => 'Edit'], 'breadcrumb');
      $tpl->merge('Blog Category', 'breadcrumb_name');
      $tpl->merge('Edit', 'breadcrumb_action');
      $tpl->merge('Update', 'button_form');
    }
}
