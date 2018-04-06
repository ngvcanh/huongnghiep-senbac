<?php
	defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
	use Library\Session;
	use Library\Validate;
	use Application\Response;

	$tokenName = 'maps_form_search_token';
	$commitName = 'Maps_Search_Commit';
	$maps = [];
	$response = ['message' => 'InData Validate', 'status' => 'error', 'title' => 'Error!', 'maps' => $maps];
	if (validate_token_commit($post, $tokenName, $commitName)){
		$response = ['message' => 'Data invalid.', 'status' => 'error', 'title' => 'Error!', 'maps' => $maps];
		$rules = [
			'address'     => ['type' => 'string', 'min' => 1, 'max' => 200],
			'match'       => ['type' => 'dble', 'min' => 1, 'max' => 10],
			'literature' 	=> ['type' => 'dble', 'min' => 1, 'max' => 10],
			'more'     		=> ['type' => 'dble', 'min' => 1, 'max' => 10],
			'about_km'    => ['type' => 'int', 'min' => 0, 'max' => 100],
			'about_point' => ['type' => 'int', 'min' => 0, 'max' => 100]
		];
		$columns = array_keys($rules);
		
		$validate = Validate::getInstance($rules, $columns)->setSource($post);
		$data = $validate->run();
		if ($validate->isFullValid()){ 
			$response['message'] = 'Không Tìm Thấy Trường';
			$sumPoint = ($post['match']*2) + ($post['literature']*2) + ($post['more']) - ($post['about_point']); 
			$model->getListLocationSchools("`point` < '{$sumPoint}'");
			$meter = $post['about_km'] * 1000;
			while($row = $model->fetch()){
				if($model->getDistanceBetweenPointsNew($post['lat'], $post['lng'], $row->lat, $row->lng) < $meter){
					$maps[] = [
						'lat' => $row->lat,
						'lng' => $row->lng
					];
				}
			}

			if(count($maps) > 0)
				$response = ['message' => 'Tìm Thành Công', 'status' => 'success', 'maps' => $maps, 'title' => 'Thành Công'];
		}
	}

	$this->response = $response;
	new Response('Content-Type: application/json', function(){
		return $this->response;
	});
