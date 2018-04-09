<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\Mailer;
use Library\TB;

class Accounts extends PublicModel{

    const REGISTER_DEFAULT = [
        'actived' => 0,
        'created_by' => 0,
        'group_id' => 0,
        'admin_id'  => 0,
        'is_moderator'  => 0,
        'is_admin' => 1,
        'account_type'  => 0
    ];

    function __construct(){
        parent::__construct();
    }

    function login($email, $passwd){
        $condition = ['email' => $email];
        $account = $this->getOne(TB::ACCOUNTS, $condition, ['email', 'password']);
        if (isset($account->password) && $this->verifyPasswd($passwd, $account->password)){
            $this->userid = $account->_id;
            return true;
        }
        return false;
    }

    function existEmail($email){
        $result = false;
        if(is_string($email)){
            $count = $this->count(TB::ACCOUNTS, ['email'=>$email]);
            $result = ($count > 0);
        }
        return $result;
    }

    function confirmPassword($passwd, $confirm){
        return is_string($passwd) && is_string($confirm) && strcmp($passwd, $confirm) === 0;
    }

    function createAccount(array $data){
        $result = null;
        $data = array_intersect_key($data, array_flip(TB::FIELD_ACCOUNTS));
        if (!empty($data)) {
            $data['password'] = $this->hashPasswd($data['password']);
            $data['created_at'] = time();
            $data = array_merge($data, self::REGISTER_DEFAULT);
            $result = $this->insert(TB::ACCOUNTS, $data);
        }
        return $result;
    }

    function getByEmail($email){
        return $this->getOne(TB::ACCOUNTS, ['email' => $email]);
    }

    function hashPasswd($pass){
        return password_hash($pass, PASSWORD_DEFAULT);
        //return md5($pass);
    }

    function sentMail(){
        $setting = $this->getOne(TB::SETTINGS, ['type' => 'sendmail']);
        
        if (isset($setting->extra)){
            $extra = $setting->extra;

            if ($extra instanceof stdClass && isset($extra->server)){
                $server = $extra->server;

                if ($server instanceof stdClass && isset($extra->from)){
                    $from = $extra->from;

                    if ($from instanceof stdClass){
                        $flag = true;

                        if (!isset($server->host) || !is_string($server->host)) $flag = false;
                    }
                }
            }
        }
    }

    private function verifyPasswd($pass, $hash){
        return password_verify($pass, $hash);
    }

}