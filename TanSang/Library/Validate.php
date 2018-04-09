<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

final class Validate{

	private static $instance;
	private $rule		= [];
	private $source 	= [];
	private $position 	= [];
	private $destroy 	= [];
	private $data 		= [];

	private function __construct($rules, $position){
		$this->setPosition($position)->setRules($rules);
	}

	static function getInstance($rules, $position = []){
		if (!isset(self::$instance)) self::$instance = new Validate($rules, $position);
		return self::$instance;
	}

	function setPosition(array $position){
		$this->position = array_flip($position);
		return $this;
	}

	function setRules(array $rules){
		foreach ($rules as $key => $value) if (is_array($value)) $this->setRule($key, $value);
		return $this;
	}

	function setRule($element, $rule){
		if (is_string($element)) $this->rule[$element] = $rule;
		return $this;
	}

	function setSource(array $source){
		$this->source = $source;
		return $this;
	}

	function setDestroy(array $destroy){
		$this->destroy = array_flip($destroy);
		return $this;
	}

	function run(){
		$this->data = [];
		if (!empty($this->position)) $this->source = array_intersect_key($this->source, $this->position);
		if (!empty($this->destroy)) $this->source = array_diff_key($this->source, $this->destroy);

		foreach ($this->source as $element => $value) 
			if ($this->doValidate($element, $value)) $this->data[$element] = $value;
		return $this->data;
	}

	function isValid(){
		return empty(array_diff_key($this->source, $this->data));
	}

	function isFullValid(){
		return empty(array_diff_key($this->position, $this->data));
	}

	function elementInvalid($enough = 0){
		$arrSource		= array_diff_key($this->source, $this->data);
		$arrPosition	= ($enough === 1) ? array_diff_key($this->position, $this->data) : [];
		return array_keys(array_merge($arrSource, $arrPosition));
	}

	private function doValidate($element, $value){
		if (!isset($this->rule[$element])) return ;
		$rule = $this->rule[$element];

		if (isset($rule['type'])){
			$method = 'validate' . ucfirst($rule['type']);
			if (method_exists($this, $method)) return $this->$method($value, $rule);
		}

		return 1;
	}

	private function validateString($value, $rule){
		if (!is_string($value)) return ;
		$length = strlen($value);
		
		if (isset($rule['min']) && $this->validateInt($rule['min'], [])){
			$min = intval($rule['min']);
			if ($length < $min) return ;
		}

		if (isset($rule['max']) && $this->validateInt($rule['max'], [])){
			$max = intval($rule['max']);
			if ($length > $max) return ;
		}
		
		if (isset($rule['base']) && !preg_match($rule['base'], $value)) return ;

		if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'])) return ;
		if (isset($rule['notin']) && is_array($rule['notin']) && in_array($value, $rule['notin'])) return ;

		return 1;
	}

	private function validateInt($value, $rule){
		if (!is_numeric($value)) return ;

		$parse = intval($value);
		if ($parse != $value) return ;

		$value = $parse;

		if (isset($rule['min']) && $this->validateInt($rule['min'], [])){
			$min = intval($rule['min']);
			if ($value < $min) return ;
		}

		if (isset($rule['max']) && $this->validateInt($rule['max'], [])){
			$max = intval($rule['max']);
			if ($value > $max) return ;
		}

		if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'])) return ;
		if (isset($rule['notin']) && is_array($rule['notin']) && in_array($value, $rule['notin'])) return ;

		return 1;
	}

	function validateDble($value, $rule){
		if (!is_double(+$value) && !is_numeric($value)) return ;
		$value = doubleval($value);

		if (isset($rule['min']) && $this->validateDble($rule['min'], [])){
			$min = doubleval($rule['min']);
			if ($value < $min) return ;
		}

		if (isset($rule['max']) && $this->validateDble($rule['max'], [])){
			$max = doubleval($rule['max']);
			if ($value > $max) return ;
		}

		if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'])) return ;
		if (isset($rule['notin']) && is_array($rule['notin']) && in_array($value, $rule['notin'])) return ;

		return 1;
	}

	function validateJson($value, $rule){
		try{
			$json = Json::decode($value);
			if (isset($rule['base']) && !preg_match($rule['base'], $value)) return ;
		}catch(Exception $error){
			return ;
		}

		return 1;
	}

	function validateIp($value, $rule){
		if (!is_string($value)) return ;
		if (filter_var($value, FILTER_VALIDATE_IP) === false) return ;

		if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'])) return ;
		if (isset($rule['notin']) && is_array($rule['notin']) && in_array($value, $rule['notin'])) return ;

		return 1;
	}

	function validateDomain($value){
		if (!is_string($value)) return ;

		$pattern = '#^(https?://)?(www\.)?([A-Za-z0-9]+[A-Za-z0-9\-]*[A-Za-z0-9]+\.){1,2}[A-Za-z]{2,}/?$#';
		if (!preg_match($pattern, $value)) return ;
		
		if (isset($rule['in']) && is_array($rule['in']) && !in_array($value, $rule['in'])) return ;
		if (isset($rule['notin']) && is_array($rule['notin']) && in_array($value, $rule['notin'])) return ;

		return 1;
	}

	function validateArr($value, $rule){
		if (!is_array($value)) return ;

		$number = count($value);

		if (isset($rule['min']) && $this->validateInt($rule['min'], [])){
			$min = intval($rule['min']);
			if ($number < $min) return ;
		}

		if (isset($rule['max']) && $this->validateInt($rule['max'], [])){
			$max = intval($rule['max']);
			if ($number > $max) return ;
		}

		if (isset($rule['base'])){
			$json = Json::encode($value);
			if (!preg_match($rule['base'], $json)) return ;
		}

		return 1;
	}

	function validateBool($value){
		return in_array($value, [true, false]);
	}

}