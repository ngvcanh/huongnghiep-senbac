<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Lib\Session;
use Lib\URL;

Session::cancel('HBSQL_Administator_Logged');
Session::cancel('HBSQL_UserInfo');

URL::redirect($urlHome);
die;