<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class Maps extends Model{

	final public function __construct(){
			parent::__construct();
	}
		
	final function getListWard($condition = [], $options = []){
		return $this->getMany(TB::WARD, TB::F_WARD, $condition, $options);
	}

	final function getListDistrict($condition = [], $options = []){
		return $this->getMany(TB::DISTRICT, TB::F_DISTRICT, $condition, $options);
	}

	final public function getDistrictById($id, array $field = []){
		$field = array_intersect_key($field, TB::F_DISTRICT);
		if (empty($field)) $field = TB::F_DISTRICT;
		return $this->getOne(TB::DISTRICT, $field, ['id' => $id]);
	}

	final public function getWardtById($id, array $field = []){
		$field = array_intersect_key($field, TB::F_WARD);
		if (empty($field)) $field = TB::F_WARD;
		return $this->getOne(TB::WARD, ['name'], ['id' => $id]);
	}

	final function getListLocationSchools($condition = [], $options = []){
		return $this->getMany(TB::LOCATION_SCHOOLS, TB::F_LOCATION_SCHOOLS, $condition, $options);
	}

	function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {

    $theta = $longitude1 - $longitude2;

    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));

    $miles = acos($miles);

    $miles = rad2deg($miles);

    $miles = $miles * 60 * 1.1515;

    $kilometers = $miles * 1.609344;

    $meters = $kilometers * 1000;

    return $meters;

	}

}