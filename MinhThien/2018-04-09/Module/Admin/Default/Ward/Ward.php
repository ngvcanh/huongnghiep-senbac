<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class Ward extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getWardById($id){
        //$hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];
        $field = array_diff(TB::F_WARD, $hidden);
        return $this->getOne(TB::WARD, $field, ['id' => $id]);
    }

    final function getListWard($condition = [], $options = []){
        return $this->getMany(TB::WARD, TB::F_WARD, $condition, $options);
    }

}