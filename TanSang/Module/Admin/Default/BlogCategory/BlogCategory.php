<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Application\Model;

final class BlogCategory extends Model{

    final public function __construct(){
        parent::__construct();
    }

    final public function getCategoryById($id){
        $hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];
        $field = array_diff(TB::F_BLOG_CATEGORY, $hidden);
        return $this->getOne(TB::BLOG_CATEGORY, $field, ['id' => $id]);
    }

    final function getListCategory($condition = [], $options = []){
        return $this->getMany(TB::BLOG_CATEGORY, TB::F_BLOG_CATEGORY, $condition, $options);
    }

    final function createCategory(array $data){
        $result = 0;
        $data = array_intersect_key($data, array_flip(TB::F_BLOG_CATEGORY));

        if (!empty($data)){
            $this->insert(TB::BLOG_CATEGORY, $data);
            $result = $this->newID();
        }

        return $result;
    }

    final function updateById($id, array $data){
        $data = array_intersect_key($data, array_flip(TB::F_BLOG_CATEGORY));

        if (!empty($data)){
            $this->update(TB::BLOG_CATEGORY, $data, ['id' => $id]);
        }
    }

    final function deleteById($id){
        if (!empty($id)){
            $this->delete(TB::BLOG_CATEGORY, ['id' => $id]);
        }
    }

}