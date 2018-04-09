<?php
	defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
	use Library\Session;
	use Library\Validate;
	use Application\Response;

	$tokenName = 'maps_form_search_token';
	$commitName = 'Maps_Search_Commit';
	$maps 		= [];
	$conetnts = [];
	$response = ['message' => 'InData Validate', 'status' => 'error', 'title' => 'Error!', 'maps' => $maps, 'conetnts' => $conetnts];
	
	if (validate_token_commit($post, $tokenName, $commitName)){
		$response = ['message' => 'Data invalid.', 'status' => 'error', 'title' => 'Error!', 'maps' => $maps, 'conetnts' => $conetnts];
		$rules = [
			'address'     => ['type' => 'string', 'min' => 1, 'max' => 200],
			'match'       => ['type' => 'dble', 'min' => 0, 'max' => 10],
			'literature' 	=> ['type' => 'dble', 'min' => 0, 'max' => 10],
			'more'     		=> ['type' => 'dble', 'min' => 0, 'max' => 10],
			'about_km'    => ['type' => 'dble', 'min' => 0, 'max' => 30],
			'about_point' => ['type' => 'dble', 'min' => -10, 'max' => 10]
		];

		$columns = array_keys($rules);
		
		$validate = Validate::getInstance($rules, $columns)->setSource($post);
		$data = $validate->run();
		if ($validate->isFullValid()){ 
			$response['message'] = 'Không Tìm Thấy Trường';
			
			$result_maps  = [
				'addreesMember'     => $post['address'],
				'point_match'       => $post['match'],
				'point_literature'  => $post['literature'],
				'point_more'        => $post['more'],
				'about_km'          => $post['about_km'],
				'about_point'       => $post['about_point'],
			];
			Session::set('result_maps', $result_maps);

			$sumPoint = ($post['match']*2) + ($post['literature']*2) + ($post['more']) - ($post['about_point']); 
			$listSchools = $model->getListLocationSchools("`point` < '{$sumPoint}'");
			$meter = $post['about_km'] * 1000;
			while($row = $model->fetch($listSchools)){
				$about = $model->getDistanceBetweenPointsNew($post['lat'], $post['lng'], $row->lat, $row->lng);
				if($about <=  $meter){
					$district   = $model->getDistrictById($row->district_id, ['name']);
    			$ward       = $model->getDistrictById($row->ward_id, ['name']);
    			$wardName   = ( isset($ward->name) ) ? ( $ward->name . ', ') : '';
    			$street     = ($row->street != '') ? $row->street . ', ' : '';

					$addreesMember = $row->address . ' ' . $street . $wardName . $district->name;
					
					$conetnts[] = [
						'address' => $addreesMember,
						'name' 		=> $row->name,
						'point'   => $row->point,
						'about' => $about
					];

					$maps[] = [
						'lat' => $row->lat,
						'lng' => $row->lng
					];
				}
			}

			if(count($maps) > 0)
				$response = ['message' => 'Tìm Thành Công', 'status' => 'success', 'maps' => $maps, 'title' => 'Thành Công', 'conetnts' => $conetnts];
		}
	}

	$this->response = $response;
	new Response('Content-Type: application/json', function(){
		return $this->response;
	});
