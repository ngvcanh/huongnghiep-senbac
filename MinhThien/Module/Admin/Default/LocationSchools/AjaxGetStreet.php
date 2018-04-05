<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;
  use Application\Response;
  use Library\File;
  
  $newTheme = "{$this->themePath}/{$config->folder}/location-schools/street.{$config->extension}";
  if(!File::isFile($newTheme)) die;

  $tpl->setTheme($newTheme);

  if(count($post) == 1 && isset($post['name'])){
    $name = $post['name'];
    $model->getAutoStreet($name);

    while($row = $model->fetch()){
      $tpl->assign($row, 'streets');
    }
  }

  new Response('Content-Type: ' . $tpl->getHeader(), function(){
    return $this->template->getContent();
  });