<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

  $tpl->setFile([
    'head' => 'accounts/login/head',
    'body' => 'accounts/login/body',
  ]);