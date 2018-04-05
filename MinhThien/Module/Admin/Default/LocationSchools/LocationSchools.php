<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class LocationSchools extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getById($id){
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

    final function getListWard($condition = [], $options = []){
        return $this->getMany(TB::WARD, TB::F_WARD, $condition, $options);
    }

    // final function createCategory(array $data){
    //     $result = 0;
    //     $data = array_intersect_key($data, array_flip(TB::F_BLOG_CATEGORY));

    //     if (!empty($data)){
    //         $this->insert(TB::BLOG_CATEGORY, $data);
    //         $result = $this->newID();
    //     }

    //     return $result;
    // }

    // final function updateById($id, array $data){
    //     $data = array_intersect_key($data, array_flip(TB::F_BLOG_CATEGORY));

    //     if (!empty($data)){
    //         $this->update(TB::BLOG_CATEGORY, $data, ['id' => $id]);
    //     }
    // }

    // final function deleteByIds(array $ids){
    //     if (!empty($ids)){
    //         $condition = "`id` IN (" . join(',', array_values($ids)) . ")";
    //         $this->delete(TB::BLOG_CATEGORY, $condition);
    //     }
    // }

}