<?php
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));
use Library\Session;

function RAU_string($str){
	$result = '';
	$str 	= explode('-', $str);

	foreach ($str as $value) $result .= ucfirst($value);
	return $result;
}

function validate_token_commit($post, $tokenName, $compareCommit){
	if (!isset($post['token']) || !is_string($post['token'])) return false;
	if (!isset($post['commit']) || !is_string($post['commit'])) return false;
	if (!is_string($compareCommit)) return false;

	$token 	= $post['token'];
	$commit = md5($post['commit']);

	$accessToken = Session::get($tokenName);
	if (is_string($accessToken)) $accessToken = md5($accessToken);
	
	if (is_array($accessToken)){
		$key = array_search($token, $accessToken);
		if ($key < 0) return false;
		$accessToken = md5($accessToken[$key]); 
	}

	return (md5($token) == $accessToken && $commit == md5($compareCommit));
}