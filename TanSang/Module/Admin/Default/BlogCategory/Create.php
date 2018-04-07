<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\URL;
use Library\Validate;
use Library\Session;
use Library\File;
use Application\Response;


$tokenName = 'blog_category_form_token';
$commitName = 'Blog_Category_Create';

if (validate_token_commit($post, $tokenName, $commitName)){

    $flag = true;
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
        $response['message'] = 'Can not create Blog category.';

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
            $result = $model->createCategory($data);
            if($result > 0){
                $response = ['message' => 'Create success.', 'status' => 'success'];
            }
        }        
    }

    $this->response = $response;
    new Response('Content-Type: application/json', function(){
        return $this->response;
    });
}

$strToken = $token->generate(32);
Session::add($tokenName, $strToken);


$listCate = $model->getListCategory(['parent' => 0]);

while($row = $model->fetch($listCate)){ 
$tpl->assign($row, 'listBlogCate');
$listSubCate = $model->getListCategory(['parent' => $row->id]);

    while($row1 = $model->fetch($listSubCate)){ 
        $tpl->assign($row1, 'listBlogCate.listSubCate');    
    }
}

$tpl->merge($strToken, $tokenName);
$tpl->merge($commitName, 'blog_category_form_commit');

$tpl->setFile([
    'content' 		=> 'blog-category/form',
    'sub_script' 	=> 'blog-category/create-script'
]);

$tpl->assign(['name' => 'Create new'], 'breadcrumb');
$tpl->merge('Blog category manager', 'breadcrumb_name');
$tpl->merge('Create new', 'breadcrumb_action');
$tpl->merge('Create', 'button_form');