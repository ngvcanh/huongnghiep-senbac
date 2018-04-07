<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Application\Model;

class Accounts extends Model{

    const TABLE = 'accounts';
    const FIELD = [
        'firstname', 'lastname', 'email', 'password', 'phone', 
        'home_address', 'created_time', 'modify_time'
    ];

    function createAccount(array $data){
        $valid = array_intersect_key($data, array_flip(self::FIELD));
        if (isset($valid['password'])) $valid['password'] = $this->hashPassword($valid['password']);
        $valid['created_time'] = time();
        return !empty($valid) ? $this->insert(self::TABLE, $valid) : NULL;
    }

    function existEmail($email){
        $result = false;
        if (is_string($email)){
            $count = $this->count(self::TABLE, ['email' => $email]);
            $result = ($count > 0);
        }
        return $result;
    }

    function existEmailDiffId($email, $id){
        $result = false;
        if (is_string($email) && $this->validId($id)){
            $condition = ['email' => $email, '_id' => ['$ne' => $this->parseId($id)]];
            $count = $this->count(self::TABLE, $condition);
            $result = ($count > 0);
        }
        return $result;
    }

    function getByEmail($email){
        return is_string($email) ? $this->getOne(self::TABLE, ['email' => $email], ['_id']) : NULL;
    }

    function getById($id){
        $result = [];
        if ($this->validId($id)){
            $condition = ['_id' => $this->parseId($id)];
            $result = $this->getOne(self::TABLE, $condition);
        }
        return $result;
    }

    function getListAccount($start, $limit){
        $cursor = $this->getMany(self::TABLE);
        $cursor->skip($start)->limit($limit);
        return $cursor;
    }

    function editAccount(array $data, $id){
        $result = NULL;
        
        if ($this->validId($id)){
            $data = array_intersect_key($data, array_flip(self::FIELD));
            
            if (!empty($data)){
                if (isset($data['password'])) $data['password'] = $this->hashPassword($data['password']);
                $data['modify_time'] = time();

                $condition = ['_id' => $this->parseId($id)];
                $result = $this->update(self::TABLE, $data, $condition);
            }
        }
        return $result;
    }

    private function hashPassword($passwd){
        return md5($passwd);
    }

}