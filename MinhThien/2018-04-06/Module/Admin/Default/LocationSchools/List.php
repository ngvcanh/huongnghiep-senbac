<?php
  defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
  use Library\URL;

  $listSchools = $model->getListLocationSchools();

  while($row = $model->fetch($listSchools)){
    $tpl->assign($row, 'listLocationSchools');

    $url_edit_location_schools        = URL::create([K_URL_DASH, $thisModule, 'edit', $row->id]);
    $url_delete_location_schools      = URL::create([K_URL_DASH, $thisModule, 'delete', $row->id]);

    $district   = $model->getDistrictById($row->district_id, ['name']);
    $ward       = $model->getWardtById($row->ward_id, ['name']);
    $wardName   = ( isset($ward->name) ) ? ( $ward->name . ', ') : '';
    $street     = ($row->street != '') ? ( $row->street . ', ') : '';

    $addrees_schools = $row->address . ' ' . $street . $wardName . $district->name;
    $tpl->assign(['addrees_schools' => $addrees_schools], 'listLocationSchools.address');
    $tpl->assign([
      'edit'    => $url_edit_location_schools,
      'delete'  => $url_delete_location_schools
    ], 
    'listLocationSchools.url_location_schools');
  }

  $tpl->assign(['name' => 'List'], 'breadcrumb');
  $tpl->merge('Location Schools', 'breadcrumb_name');
  $tpl->merge('List', 'breadcrumb_action');
  $tpl->merge('Data List Location Schools', 'breadcrumb_title');

  $tpl->setFile([
    'content' 		  => $thisModule . '/list',
    'sub_script' 		=> $thisModule . '/list-script'
  ]);