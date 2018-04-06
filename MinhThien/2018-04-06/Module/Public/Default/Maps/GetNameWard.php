<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;
  use Application\Response;
  use Library\File;
  
  $newTheme = "{$this->themePath}/{$config->folder}/maps/index/ward.{$config->extension}";
  if(!File::isFile($newTheme)) die;

  $tpl->setTheme($newTheme);

  if(count($post) == 1 && isset($post['id']) && +$post['id'] > 0){
    $id = $post['id'];
    $model->getListWard(['district_id' => $id]);
    while($row = $model->fetch()){
      $tpl->assign($row, 'listWard');
    }
  }

  new Response('Content-Type: ' . $tpl->getHeader(), function(){
    return $this->template->getContent();
  });