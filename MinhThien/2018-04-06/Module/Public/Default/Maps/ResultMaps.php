<?php
	defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
	use Library\Session;
	use Library\URL;
	
	$result_maps = Session::get('result_maps');

	$district   = $model->getDistrictById($result_maps['district_id'], ['name']);
  $ward       = $model->getDistrictById($result_maps['ward_id'], ['name']);
	$wardName   = ( isset($ward->name) ) ? ( $ward->name . ', ') : '';
  $street     = $result_maps['street'];

	$addreesMember = $result_maps['address'] . ' ' . $street . ', ' . $wardName . $district->name;
	$result_maps['addreesMember'] = $addreesMember;

	$tokenName = 'maps_form_search_token';
	$commitName = 'Maps_Search_Commit';
	$url_result = URL::create(['maps', 'result-schools']);

	$strToken = $token->generate(32);
	Session::add($tokenName, $strToken);

	$tpl->merge($strToken, $tokenName);
	$tpl->merge($commitName, 'maps_search_commit');

	$tpl->merge($result_maps, 'result_maps');
	$tpl->merge($url_result, 'url_result');
	$tpl->merge('Maps', 'title_project');


	$tpl->setFile([
			'head'  			=> 'maps/result/head',
			'body'				=> 'maps/result/body',
			'sub_script' 	=> 'maps/result/script'
	]);
