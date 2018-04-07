<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class Ward extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getWardById($id){
        return $this->getOne(TB::WARD, TB::F_WARD, ['id' => $id]);
    }

    final function getListWard($condition = [], $options = []){
        return $this->getMany(TB::WARD, TB::F_WARD, $condition, $options);
    }

    function createWard(array $data){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_WARD)); 
        if (!empty($data)) {
            $this->insert(TB::WARD, $data);    
            $result = $this->newID();       
        }
        return $result;
    }

    function updateWardById($id, array $data){
        $data = array_intersect_key($data, array_flip(TB::F_WARD)); 
    
        if (!empty($data)) {
            $this->update(TB::WARD, $data, ['id' => $id]);           
        }
    }

    function deleteWardById($id){
        return $this->delete(TB::WARD, ['id' => $id]);
    }

    final function getListDistrict($condition = [], $options = []){
        return $this->getMany(TB::DISTRICT, TB::F_DISTRICT, $condition, $options);
    }

}