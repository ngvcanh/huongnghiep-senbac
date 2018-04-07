<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

$tpl->merge('Trang Chủ', 'title_project');

$tpl->setFile([
		'content' => 'home/content',
		'script'	=> 'home/script',
		'style'		=> 'home/style'
]);
