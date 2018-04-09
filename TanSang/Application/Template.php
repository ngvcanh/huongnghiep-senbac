<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\Json;

final class Template{

	private static $template;
	private $loop 	= [];
	private $var 	= [];
	private $include = [];
	private $box 	= [];
	private $theme;
	private $folder;
	private $extension;
	private $header = 'text/html';
	private $content;

	private function __construct(){}

	static function init(){
		if (!isset(self::$template)) self::$template = new Template();
		return self::$template;
	}

	function setTheme($path){
		$this->theme = $path;
		$this->content = File::getContent($this->theme);
		return $this;
	}

	function setFolder($folder){
		$this->folder = $folder;
	}

	function setExtension($ext){
		$this->extension = $ext;
		return $this;
	}

	function setHeader($header){
		$this->header = $header;
	}

	function getHeader(){
		return $this->header;
	}

	function getContent($format = true){
		if ( $format === true) $this->format();
		return $this->content;
	}

	function box($box){
		if (is_string($box) && !!$box) $this->box[] = $box;
	}

	function setFile(array $files){
		if (empty($files))
			throw new ApplicationException("Template file is not empty.", 0, 0, __FILE__, __LINE__);

		foreach ($files as $key => $value){
			if (!is_string($value) || !$value)
				throw new ApplicationException("Template file {$key} is just string.", 0, 0, __FILE__, __LINE__);
			$pathFile = join(K_DS, [$this->folder, $value . '.' . $this->extension]);

			if (!File::isFile($pathFile))
				throw new ApplicationException("Template file {$value}.{$this->extension} is not exist.", 0, 0, __FILE__, __LINE__);
			$this->include[$key] = $pathFile;
		}
	}

	function merge($var, $key){
		if (!is_string($key) || !$key)
			throw new ApplicationException("Template merge key {$key} invalid.", 0, 0, __FILE__, __LINE__);
		$this->var[$key] = $var;
	}

	function assign($var, $key){
		$this->addLoop($var, $key);
	}

	private function addLoop($var, $key, $loop = NULL){
		if (!is_string($key) || !$key)
			throw new ApplicationException("Template assign key {$key} invalid.", 0, 0, __FILE__, __LINE__);

		if (!is_object($var) && !is_array($var))
			throw new ApplicationException("Template assign variable {$key} invalid.", 0, 0, __FILE__, __LINE__);

		if ($loop == NULL) $loop = is_array($this->loop) ? $this->loop : clone $this->loop;
		$assign = is_array($var) ? $var : clone $var;

		$arrKey 	= explode('.', trim($key, '.'));
		$firstKey 	= array_shift($arrKey);

		if (!empty($arrKey)){
			$newKey = join('.', $arrKey);

			if (is_array($loop)){
				if (!isset($loop[$firstKey])) $loop[$firstKey] = [[]];
				$position = count($loop[$firstKey]) - 1;

				$deLoop = $loop[$firstKey][$position];
				$deLoop = ($deLoop === NULL) ? [] : $deLoop;

				$this->addLoop($assign, $newKey, $deLoop);
				$loop[$firstKey][$position] = is_array($this->loop) ? $this->loop : clone $this->loop;
			}elseif (is_object($loop)){
				if (!isset($loop->{$firstKey})) $loop->{$firstKey} = [[]];
				$position = count($loop->{$firstKey}) - 1;

				$deLoop = $loop->{$firstKey}[$position];
				$deLoop = ($deLoop === NULL) ? [] : $deLoop;

				$this->addLoop($assign, $newKey, $deLoop);
				$loop->{$firstKey}[$position] = is_array($this->loop) ? $this->loop : clone $this->loop;
			}
		}else{
			if (is_array($loop)){
				if (!isset($loop[$firstKey])) $loop[$firstKey] = [];
				$current = $loop[$firstKey];

				if (is_object($current)) $current = (array)$current;
				elseif (!is_array($current)) $current = [];

				$current[] = is_array($assign) ? $assign : clone $assign;
				$loop[$firstKey] = $current;
			}else if (is_object($loop)){
				if (!isset($loop->{$firstKey})) $loop->{$firstKey} = [];
				$current = $loop->{$firstKey};

				if (is_object($current)) $current = (array)$current;
				elseif (!is_array($current)) $current = [];

				$current[] = is_array($assign) ? $assign : clone $assign;
				$loop->{$firstKey} = $current;
			}
		}

		$this->loop = is_array($loop) ? $loop : clone $loop;
	}

	private function replaceInclude(){
		$partten = "/<k:inc\s+([\w\.]+)\s*>/misU";

		if (preg_match_all($partten, $this->content, $match)){
			$arrMatch = $match[1];

			foreach($arrMatch as $key => $value){
				$text = '';

				if (isset($this->include[$value])){
					$file = $this->include[$value];
					if (File::isFile($file)) $text = file_get_contents($file);
				}

				$this->content = str_replace($match[0][$key], $text, $this->content);
			}

			$this->replaceInclude();
		}
	}

	private function replaceBox($content){
		$pattern = '/<k:if\s+(\w+)\s*>(.*)(<k:else\s+\\1\s*>(.*))?<\/k:if\s+\\1\s*>/misU';

		if (preg_match_all($pattern, $content, $matches)){
			$originHtml 	= $matches[0];
			$boxNames		= $matches[1];
			$insideIfHtml	= $matches[2];
			$insideElseHtml = $matches[4];

			foreach ($boxNames as $key => $boxName){
				$textReplace = in_array($boxName, $this->box) ? $insideIfHtml[$key] : $insideElseHtml[$key];
				$content = str_replace($originHtml[$key], $textReplace, $content);
			}

			$content = $this->replaceBox($content);
		}

		return $content;
	}

	private function replaceVarIf(){
		$pattern = '/<k:if:var\s+([\w\.]+)\s*>(.*)(<k:else:var\s+\\1\s*>(.*))?<\/k:if:var\s+\\1\s*>/misU';

		if (preg_match_all($pattern, $this->content, $matches)){
			$originHtml 	= $matches[0];
			$boxNames		= $matches[1];
			$insideIfHtml	= $matches[2];
			$insideElseHtml = $matches[4];

			foreach ($boxNames as $key => $boxName){
				$arrBoxName = explode('.', trim(trim($boxName, '.')));
				$current 	= $this->var;
				$flag 		= true;

				foreach ($arrBoxName as $value){
					if (is_array($current)){
						if (!isset($current[$value])){
							$flag = false;
							break;
						}
						$current = $current[$value];
					}else if (is_object($current)){
						if (!isset($current->{$value})){
							$flag = false;
							break;
						}
						$current = $current->{$value};
					}else{
						$flag = false;
						break;
					}
				}

				$textReplace = ($flag && !!$current) ? $insideIfHtml[$key] : $insideElseHtml[$key];
				$this->content = str_replace($originHtml[$key], $textReplace, $this->content);
			}

			$this->replaceVarIf();
		}
	}

	private function replaceVariable($content = NULL){
		$partten = "/\{k:var\s+([\w\.]+)\s*\}/";

		if (preg_match_all($partten, $this->content, $match)){
			$arrVar = $match[1];

			foreach($arrVar as $key => $value){
				$arrVal 	= explode('.', trim(trim($value), '.'));
				$current 	= $this->var;

				$text 	= '';
				$numVar = count($arrVal);
				$step 	= 0;

				do{
					$name 	= $arrVal[$step];
					++$step;

					if (is_object($current) || is_array($current)){
						if (is_object($current) && isset($current->{$name})){
							$varVal = is_object($current->{$name}) ? (clone $current->{$name}) : $current->{$name};
						}else if (is_array($current) && isset($current[$name])){
							$varVal = is_object($current[$name]) ? (clone $current[$name]) : $current[$name];
						}else break;
					}else break;

					if ($step < $numVar){
						if (is_array($varVal) || is_object($varVal)) 
							$current = is_object($varVal) ? (clone $varVal) : $varVal;
						else break;
					}else{
						if (!is_array($varVal) && !is_object($varVal)){
							if (is_string($varVal)) $text = $varVal;
							else $text = (is_numeric($varVal) || is_double($varVal) || is_bool($varVal)) ? 
							Json::decode($varVal) : $varVal;
						}
					}
				}while($numVar - $step > 0);

				$this->content = str_replace($match[0][$key], $text, $this->content);
			}
			$this->replaceVariable();
		}
	}

	private function replaceFor(){
		$pattern = '/<k:for\s+([\w\.]+)\s*>(.*)<\/k:for\s+\\1\s*>/misU';

		if (preg_match_all($pattern, $this->content, $matches)){
			$arrOutside	= $matches[0];
			$arrNames 	= $matches[1];
			$arrInside 	= $matches[2];

			foreach ($arrNames as $key => $names){
				$arrName 	= explode('.', trim(trim($names), '.'));
				$strInside 	= $arrInside[$key];
				$text 		= '';

				$varVal 	= $this->getValueDeep($arrName, $this->var, 'arr');
				$ifContent 	= $this->replaceIfLoop($strInside, 'for', $names, $varVal);

				$mKeys = [];
				$pnKey = '/\{k:for\s+' . $names . '\.key\s*\}/misU';
				if (preg_match_all($pnKey, $strInside, $mKeys)) $mKeys = $mKeys[0];

				$arrFullVar 	= [];
				$arrChildVar	= [];

				$pnVal = '/\{k:for\s+' . $names . '\.value((\.\w+)*)\s*\}/misU';
				if (preg_match_all($pnVal, $ifContent, $mVals)){
					$arrFullVar 	= $mVals[0];
					$arrChildVar	= $mVals[1];
				}

				foreach ($varVal as $varKey => $varValue){
					$strItem = $ifContent;
					foreach ($mKeys as $mKey) $strItem = str_replace($mKeys, $varKey, $strItem);

					foreach ($arrChildVar as $childKey => $childVal){
						$childText 	= '';

						if (is_array($varValue) || is_object($varValue)){
							$arrChildKey 	= explode('.', trim(trim($childVal), '.'));
							$childText 		= $this->getValueDeep($arrChildKey, $varValue, 'str');
						}else if ($childVal == '') $childText = $varValue;

						$strItem = str_replace($arrFullVar[$childKey], $childText, $strItem);
					}
					
					$text .= $strItem;
				}

				$this->content = str_replace($arrOutside[$key], $text, $this->content);
			}
			
			$this->replaceFor();
		}
	}

	private function replaceEach($content, $data, $parent = NULL){
		if ($parent !== NULL) $parent .= '\s*\.\s*';
		$pattern = '/<k:each\s+' . $parent . '(\w+)\s*>(.*)<\/k:each\s+' . $parent . '\\1\s*>/misU';

		if (preg_match_all($pattern, $content, $matches)){
			$outsides 	= $matches[0];
			$arrNames 	= $matches[1];
			$insides 	= $matches[2];

			foreach ($arrNames as $keyRoot => $names){
				$inside = $this->replaceEach($insides[$keyRoot], $data);
				$text 	= '';

				$currentData = [];
				if (is_array($data)) $currentData = $data;
				else if (is_object($data)) $currentData = (array)$data;

				if (isset($data[$names])){
					$listData 	= $data[$names];
					$strInside 	= '';

					foreach ($listData as $itemKey => $itemData){
						$subParent 	= $parent . $names;

						$ifContent 	= $this->replaceIfLoop($inside, 'each', $subParent, $itemData);
						$varPattern = '/\{k:each\s+' . $subParent . '\s*\.\s*(\w+)\s*\}/misU';

						if (preg_match_all($varPattern, $ifContent, $varMatches)){
							$varsFull = $varMatches[0];
							$varsName = $varMatches[1];
							$strItems = $ifContent;

							foreach ($varsName as $varKey => $varName){
								if (is_object($itemData)) $itemData = (array)$itemData;
								$val = isset($itemData[$varName]) ? $itemData[$varName] : '';

								if (!is_array($val) && !is_object($val)){
									$val = (is_numeric($val) || is_double($val) || is_bool($val)) ? 
									Json::decode($val) : $val;
								}else $val 	= '';

								$strItems 	= str_replace($varsFull[$varKey], $val, $strItems);
							}

							$strInside .= $strItems;
						}else $strInside.= $ifContent;

						$strInside = $this->replaceEach($strInside, $itemData, $subParent);
					}

					$text .= $strInside;
				}

				$content = str_replace($outsides[$keyRoot], $text, $content);
			}
		}
		return $content;
	}

	private function replaceIfLoop($content, $type, $parent, $data){
		$ifPattern 	= '/\{k:' . $type . ':if\s+' . $parent . '\s*:\s*([\w\.]+)\s*\}(.*)(\{k:' . $type . ':else\s+' . $parent . '\s*:\s*\\1\s*\}(.*))?\{\/k:' . $type . ':if\s+' .  $parent. '\s*:\s*\\1\s*\}/misU';

		if (preg_match_all($ifPattern, $content, $ifMatches)){
			$ifOutside 	= $ifMatches[0];
			$ifNames	= $ifMatches[1];
			$ifInside	= $ifMatches[2];
			$elInside	= $ifMatches[4];

			foreach ($ifNames as $ifKey => $ifNames){
				$arrIfName 	= explode('.', trim(trim($ifNames), '.'));
				$ifFlag 	= $this->getValueDeep($arrIfName, $data);
				$ifText 	= $ifFlag ? $ifInside[$ifKey] : $elInside[$ifKey];
				$content 	= str_replace($ifOutside[$ifKey], $ifText, $content);
			}

			$content = $this->replaceIfLoop($content, $type, $parent, $data);
		}

		return $content;
	}

	private function getValueDeep($arrName, $data, $type = 'bool'){
		$result = NULL;
		$flag 	= true;
		
		foreach ($arrName as $name){
			if (is_object($data)) $data = (array)$data;
			if (is_array($data)){
				if (!isset($data[$name])){
					$flag = false;
					break;
				}
				$data = $data[$name];
			}else{
				$flag = false;
				break;
			}
		}

		if ($type == 'bool') $result = ($flag && !!$data);
		elseif ($type == 'arr') $result = $flag ? $data : [];
		elseif ($type == 'str'){
			$result = '';
			if ($flag && !is_object($data) && !is_array($data)){
				$result = (is_numeric($data) || is_double($data) || is_bool($data)) ? 
				Json::decode($data) : $data;
			}
		}

		return $result;
	}

	private function format($format = 0){
		$this->replaceInclude();
		$this->content = $this->replaceBox($this->content);
		$this->replaceVarIf();
		$this->content = $this->replaceEach($this->content, $this->loop);
		$this->replaceFor();

		$pattern = "/<k:not\s+(\w+)\s*>(.*)<\/k:not\s+\\1\s*>/misU";
		preg_match_all($pattern, $this->content, $matches);

		$this->replaceVariable();
		if (!empty($matches) && preg_match_all($pattern, $this->content, $match)){
			$outside = $match[0];
			foreach ($outside as $key => $value){
				$this->_xhtml = str_replace($outside[$key], htmlentities($matches[2][$key]), $this->content);
			}
		}

		if ($format === 0) $this->format(1);
	}
}