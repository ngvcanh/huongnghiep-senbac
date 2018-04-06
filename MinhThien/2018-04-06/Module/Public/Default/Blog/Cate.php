<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

$tpl->merge('Danh Mục', 'title_project');
$tpl->setFile([
    'content'   => 'cate/content',
    'style'     => 'cate/style',
]);
