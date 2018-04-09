<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

$tpl->setFile([
    'content' => 'error/404'
]);

$tpl->assign(['name' => 'Not found'], 'breadcrumb');
$tpl->merge('Error page', 'breadcrumb_name');
$tpl->merge('Page not found', 'breadcrumb_action');

if (isset($dirURL[2]) && is_numeric($dirURL[2])){
    $id = $dirURL[2];
    $cate = $model->getCategoryById($id);

    if (isset($cate->id)){
        $tpl->setFile([
            'content' => 'blog-category/form'
        ]);

        $tpl->assign(['name' => 'Edit'], 'breadcrumb');
        $tpl->merge('Blog category manager', 'breadcrumb_name');
        $tpl->merge('Edit', 'breadcrumb_action');
        $tpl->merge('Update', 'button_form');
    }
}

