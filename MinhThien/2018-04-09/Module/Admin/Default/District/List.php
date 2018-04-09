<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;

  $listDistrict = $model->getListDistrict();

  while($row = $model->fetch($listDistrict)){
    $tpl->assign($row, 'listDistrict');

    $url_edit_district        = URL::create([K_URL_DASH, $thisModule, 'edit', $row->id]);
    $url_delete_district      = URL::create([K_URL_DASH, $thisModule, 'delete', $row->id]);

    $tpl->assign([
      'edit'    => $url_edit_district,
      'delete'  => $url_edit_district
    ], 
    'listDistrict.url_district');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('District List', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List District', 'breadcrumb_title');

  $tpl->setFile([
    'content' 		  => $thisModule . '/list',
    'sub_script' 		=> $thisModule . '/list-script',
    'sub_style' 		=> $thisModule . '/list-style',
  ]);