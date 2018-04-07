<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;
  use Application\Response;

  $response = '<option selected="selected" value="0">--- Select Ward ---</option>';

  if(count($post) == 1 && isset($post['id']) && +$post['id'] > 0){
    $id = $post['id'];
    $model->getListWard(['district_id' => $id]);
    while($row = $model->fetch()){
      $response .= '<option value="'. $row->name .'">'. $row->name .'</option>';
    }
  }


  $this->response = $response;
	new Response('Content-Type: text/html', function(){
		return $this->response;
  });