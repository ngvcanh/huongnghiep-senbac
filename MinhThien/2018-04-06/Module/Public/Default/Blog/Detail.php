<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
use Library\URL;
$fullUrlCurrent = URL::getCurrentFull();

$tpl->merge('Chi Tiết Blog', 'title_project');
$tpl->merge($fullUrlCurrent, 'fullUrlCurrent');

$tpl->setFile([
    'content' => 'blog/content',
    'right'   => 'blog/right',
    'left'    => 'blog/left',
]);
