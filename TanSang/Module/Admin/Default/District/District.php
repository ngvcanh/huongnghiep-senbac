<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class District extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getById($id){
        return $this->getOne(TB::DISTRICT, TB::F_DISTRICT, ['id' => $id]);
    }

    final function getListDistrict($condition = [], $options = []){
        return $this->getMany(TB::DISTRICT, TB::F_DISTRICT, $condition, $options);
    }

    function createDistrict(array $data){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_DISTRICT)); 
        if (!empty($data)) {
            $this->insert(TB::DISTRICT, $data);    
            $result = $this->newID();       
        }
        return $result;
    }

    function updateDistrictById($id, array $data){
        $data = array_intersect_key($data, array_flip(TB::F_DISTRICT)); 
        
        if (!empty($data)) {
            $this->update(TB::DISTRICT, $data, ['id' => $id]);           
        }
    }

    function deleteDistrictById($id){
        return $this->delete(TB::DISTRICT, ['id' => $id]);
    }
}