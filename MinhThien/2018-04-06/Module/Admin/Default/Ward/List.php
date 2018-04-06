<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;

  $listWard = $model->getListWard();

  while($row = $model->fetch($listWard)){
    $tpl->assign($row, 'listWard');

    $url_edit_ward       = URL::create([K_URL_DASH, $thisModule, 'edit', $row->id]);
    $url_delete_ward    = URL::create([K_URL_DASH, $thisModule, 'delete', $row->id]);

    $tpl->assign([
      'edit'    => $url_edit_ward,
      'delete'  => $url_delete_ward
    ], 
    'listWard.url_ward');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('Ward', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List Ward', 'breadcrumb_title');

  $tpl->setFile([
    'content' 		  => $thisModule . '/list',
    'sub_script' 		=> $thisModule . '/list-script',
    'sub_style' 		=> $thisModule . '/list-style',
  ]);