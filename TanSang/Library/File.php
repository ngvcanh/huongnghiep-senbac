<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

final class File{

	static function exist($path){
		return is_string($path) && file_exists($path);
	}

	static function isFile($path){
		return self::exist($path) && is_file($path);
	}

	static function isDir($path){
		return self::exist($path) && is_dir($path);
	}

	static function mkDir($path, $mode = 0755){
		if (!self::exist($path) && is_string($path)) @mkdir($path, $mode);
		return self::isDir($path);
	}

	static function remove($path, $all = 0){
		$result = false;
		if (self::exist($path)){
			if (self::isDir($path)){
				$child 	= self::scan($path);
				$remove = true;

				if (count($child) > 2){
					$remove = false;

					if ($all === 1){
						foreach ($child as $name){
							if ($name == '.' || $name == '..') continue;
							$remove = self::remove($path . K_DS . $name);
							if (!$remove) break;
						}
					}
				}
				if ($remove) @rmdir($path);
			}else @unlink($path);
			
			$result = !self::exist($path);
		}
		return $result;
	}

	static function parent($path, $deep = 1){
		return ($deep > 1) ? dirname(self::parent($path, --$deep)) : dirname($path);
	}

	static function scan($path){
		return self::isDir($path) ? scandir($path) : [];
	}

	static function getContent($path){
		return self::isFile($path) ? @file_get_contents($path) : null;
	}

	static function open($path, $mode){
		return @fopen($path, $mode);
	}

	static function write($handle, $content){
		if ($handle){
			fwrite($handle, $content);
			self::close($handle);
		}
	}

	static function close($handle){
		if ($handle) fclose($handle);
	}
}