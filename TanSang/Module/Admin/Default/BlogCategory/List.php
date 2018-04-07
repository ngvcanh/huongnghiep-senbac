<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;

  $tokenCreateName        = 'blog_category_form_token';
  $commitCreateName       = 'Blog_Category_Form_Commit';

  $listCate = $model->getListCategory();

  while($row = $model->fetch($listCate)){
    $tpl->assign($row, 'listBlogCate');

    $thisModule = 'blog-category';
    $url_edit_cate_blog = URL::create([K_URL_DASH, $thisModule, 'edit',$row->id]);
    $url_delete_cate_blog = URL::create([K_URL_DASH, $thisModule, 'delete',$row->id]);
    
    $tpl->assign([
      'edit' => $url_edit_cate_blog,
      'delete' => $url_delete_cate_blog
    ], 
    'listBlogCate.url_cate_blog');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('Blog category manager', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List Categoty', 'breadcrumb_title');

  $strToken = $token->generate(32);
  Session::add($tokenCreateName, $strToken);

  $tpl->merge($strToken, $tokenCreateName);
  $tpl->merge($commitCreateName, 'blog_category_form_commit');

  $tpl->setFile([
    'content' 		=> 'blog-category/list',
    'sub_script' 		=> 'blog-category/list-script',
    'sub_style' 		=> 'blog-category/list-style',
  ]);