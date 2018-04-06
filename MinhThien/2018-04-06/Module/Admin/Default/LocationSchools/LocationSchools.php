<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class LocationSchools extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getLocationById($id){
        $hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];
        $field = array_diff(TB::F_LOCATION_SCHOOLS, $hidden);
        return $this->getOne(TB::LOCATION_SCHOOLS, $field, ['id' => $id]);
    }

    final function getListLocationSchools($condition = [], $options = []){
        return $this->getMany(TB::LOCATION_SCHOOLS, TB::F_LOCATION_SCHOOLS, $condition, $options);
    }

    final function getAutoStreet($name){
        $condition = "`street` LIKE '{$name}%'";
        return $this->getMany(TB::LOCATION_SCHOOLS, ['street'], $condition);
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

    final function getListWard($condition = [], $options = []){
        return $this->getMany(TB::WARD, TB::F_WARD, $condition, $options);
    }

    final function createSchool(array $data){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_LOCATION_SCHOOLS)); 
        if (!empty($data)) {
            $data['created_at'] = time();
            $result = $this->insert(TB::LOCATION_SCHOOLS, $data);
        }
        return $result;
    }

    function updateSchoolByID(array $data, $id){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_LOCATION_SCHOOLS)); 

        if (!empty($data)) {
            $data['updated_at'] = time();
            $result = $this->update(TB::LOCATION_SCHOOLS, $data, ['id' => $id] );
            $result = $this->newID();
        }
        return $result;
    }

    final function deleteByIds(array $ids){
        if (!empty($ids)){
            $condition = "`id` IN (" . join(',', array_values($ids)) . ")";
            $this->delete(TB::LOCATION_SCHOOLS, $condition);
        }
    }

}