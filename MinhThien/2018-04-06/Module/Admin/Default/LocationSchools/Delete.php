<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Application\Response;

  $response = ['message' => 'Delete Error', 'status' => 'error', 'title' => 'Error'];

  if (isset($dirURL[2]) && is_numeric($dirURL[2])){
    $id = $dirURL[2];

    $result = $model->deleteByIds([$id]);
    $response = [
      'message' => 'Delete success',
      'status'  => 'success', 
      'title'   => 'Success',
      'url'     => $urlList
    ];
  }

  $this->response = $response;
  new Response('Content-Type: application/json', function(){
    return $this->response;
  });


