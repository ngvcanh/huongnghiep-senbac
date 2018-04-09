<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class Vocation extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final function getListVocationGroup(){
        $options = ['sort' => ['ordering' => 'ASC']];
        return $this->getMany(TB::GROUP_VOCATIONS, TB::F_GROUP_VOCATION, NULL, $options);
    }
    final function getGroup($id){
        
        return $this->getOne(TB::GROUP_VOCATIONS, TB::F_GROUP_VOCATION, ['id' => "{$id}"]);
    }

    final function getListVocationQuestion(){
        $options = ['sort' => ['ordering' => 'ASC']];
        return $this->getMany(TB::QUESTION_VOCATIONS, TB::F_QUESTION_VOCATION, NULL, $options);
    }

    final function getQuestion($id){
        return $this->getOne(TB::QUESTION_VOCATIONS, TB::F_QUESTION_VOCATION, ['id' => "{$id}"]);
    }

    function createGroup(array $data){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_GROUP_VOCATION)); 
        if (!empty($data)) {
            $this->insert(TB::GROUP_VOCATIONS, $data);    
            $result = $this->newID();       
        }
        return $result;
    }

    function updateGroupById($id, array $data){
        $data = array_intersect_key($data, array_flip(TB::F_GROUP_VOCATION)); 
        if (!empty($data)) {
            $this->update(TB::GROUP_VOCATIONS, $data, ['id' => $id]);           
        }
    }

    function deleteGroupById($id){
        $this->delete(TB::GROUP_VOCATIONS, ['id' => $id]);           
    }

    function createQuestion(array $data){
        $result = null; 
        $data = array_intersect_key($data, array_flip(TB::F_QUESTION_VOCATION)); 
        if (!empty($data)) {
            $this->insert(TB::QUESTION_VOCATIONS, $data);    
            $result = $this->newID();       
        }
        return $result;
    }

    function updateQuestionById($id, array $data){
        $data = array_intersect_key($data, array_flip(TB::F_QUESTION_VOCATION)); 
        if (!empty($data)) {
            $this->update(TB::QUESTION_VOCATIONS, $data, ['id' => $id]);           
        }
    }

    function deleteQuestionById($id){
        $this->delete(TB::QUESTION_VOCATIONS, ['id' => $id]);           
    }

}