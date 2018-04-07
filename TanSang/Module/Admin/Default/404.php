<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

  $tpl->setFile([
    'content' => 'error/404',
  ]);
  $tpl->merge('Page not found', 'site_title');