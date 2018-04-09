<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

final class Json{

	static function loadFromFile($path, $assoc = false){
		$result = NULL;

		if (File::isFile($path)){
			$result = File::getContent($path);
			if ($result) $result = self::decode($result, $assoc);
		}

		return $result;
	}

	static function encode($param){
		return json_encode($param);
	}

	static function decode($string, $assoc = false){
		$assoc 	= !!$assoc ? true : false;
		return json_decode($string, $assoc, 512, JSON_BIGINT_AS_STRING);
	}

	static function lastError(){
		return json_last_error();
	}

	static function getMessage(){
		return json_last_error_msg();
	}

	static function toTree($json, $parse = 0, $tab = 0, $tree = 1){
		$parse 	= ($parse === 1) ? 1 : 0;
		$flag 	= ($tree === 1);
		$strTab = '';

		$t = '';
		$n = '';
		$s = ':';

		if ($flag){
			$tab 	= intval($tab);
			$strTab = str_repeat("\t", $tab);

			$t = "\t";
			$n = "\n";
			$s = ' : ';
		}

		if ($parse && is_string($json)) $json = self::decode($json);

		$result = '';
		$type 	= gettype($json);

		switch ($type){
			case 'string':
				$decode  = self::decode($json);
				$result .= (is_bool($decode) || is_numeric($decode) || is_float($decode) || is_string($decode)) ? 
				self::encode($decode) : self::encode($json);
				break;
			case 'boolean':
				$result .= self::encode($json);
				break;
			case 'integer':
			case 'double':
				$result .= $json;
				break;
			case 'NULL':
				$result .= 'null';
				break;
			case 'array':
				$json = self::decode(self::encode($json));
				if (is_array($json)){
					$result .= "[";
					$arr = 0;
					
					foreach ($json as $value){
						$arr++;
						$result .= $n . $strTab . $t . self::toTree($value, $parse, $tab + 1, $tree) . ",";
					}
					
					if ($arr > 0) $result = rtrim($result, ",") . $n . $strTab;
					$result .= "]";
				}
				else $result .= self::toTree($json, $parse, $tab, $tree);
				break;
			case 'object':
				if (!is_callable($json)){
					$result .= "{";
					$obj = 0;
					
					foreach ($json as $key => $value){
						$obj++;
						$result .= $n . $strTab . $t . '"' . $key . '"' . $s . self::toTree($value, $parse, $tab + 1, $tree) . ",";
					}
					
					if ($obj > 0) $result = rtrim($result, ",") . $n . $strTab;
					$result .= "}";
				}
				else $result .= 'null';
				break;
		}

		return $result;
	}

}