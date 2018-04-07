<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

class Token{
	private static $instance;
	protected $_alphabet;
	protected $_length;

	private function __construct($alphabet = ''){
		if ('' !== $alphabet) $this->setAlphabet($alphabet);
		else $this->setAlphabet(
				implode('', range('a', 'z'))
			.	implode('', range('A', 'Z'))
			.	implode('', range(0, 9))
		);
	}

	static function getInstance($alphabet = ''){
		if (!isset(self::$instance)) self::$instance = new Token($alphabet);
		return self::$instance;
	}

	public function setAlphabet($alphabet){
		$this->_alphabet = $alphabet;
		$this->_length = strlen($alphabet);
	}

	public function generate($length){
		$token = '';
		for ($i = 0; $i < $length; $i++){
			$randomKey = $this->getRandomInteger(0, $this->_length);
			$token .= $this->_alphabet[$randomKey];
		}
		return $token;
	}

	private function getRandomInteger($min, $max){
		$range = $max - $min;
		if ($range < 0) return $min;
		$log = log($range, 2);
		$bytes = (int) ($log / 8) + 1;
		$bits = (int) $log + 1;
		$filter = (int) (1 << $bits) - 1;
		do{
			$rand = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rand = $rand & $filter;
		}while ($rand >= $range);
		return $min + $rand;
	}
}