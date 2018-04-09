<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\TB;
use Library\Session;
use Application\Model;
use MongoDB\Driver\WriteResult;

class PublicModel extends Model{

    protected function __construct(){
        parent::__construct();
    }

    function getMyAccount(){
        $result = new stdClass;
        $id = Session::get(SESSION_USER_KEY);
        
        if ($id !== NULL) $result = $this->getOne(TB::ACCOUNTS, ['_id' => $id]);
        return $result;
    }

    function getListAccount($myAdmin){
        $result = [];
        if ($myAdmin !== 0){
            $condition = ['actived' => 1, 'admin_id' => $myAdmin];
            $result = $this->getMany(TB::ACCOUNTS, $condition);
        }
        return $result;
    }

    function isDBResult($result){
        return $result instanceof WriteResult;
    }

}