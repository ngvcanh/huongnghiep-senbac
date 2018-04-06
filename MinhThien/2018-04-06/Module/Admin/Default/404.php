<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

  $tpl->merge('Page Not Fount', 'site_title');

  $tpl->setFile([
    'content' => 'error/404'
  ]);