<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Application\Model;

class User extends Model{

    const TB = 'hb_user';

    function __construct(){
        parent::__construct();
        $this->connect = $this->db->connection(K_SERV_HOST, K_SERV_USER, K_SERV_PASS, K_SERV_DATA);
    }

    function login($username, $password){
        $result = false;
		$user 	= $this->getItem(self::TB, ['username' => $username]);

		if (isset($user->password) && md5($password) == $user->password && $user->status == 1){
			$this->info = $user;
			$result = true;
		}
		return $result;
    }

}