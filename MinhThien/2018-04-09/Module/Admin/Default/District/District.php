<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class District extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getById($id){
        //$hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];
        $field = array_diff(TB::F_DISTRICT, $hidden);
        return $this->getOne(TB::DISTRICT, $field, ['id' => $id]);
    }

    final function getListDistrict($condition = [], $options = []){
        return $this->getMany(TB::DISTRICT, TB::F_DISTRICT, $condition, $options);
    }

}