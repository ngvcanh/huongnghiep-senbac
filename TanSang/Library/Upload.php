<?php
namespace Library;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

class Upload{

	private $path;
	private $extension;
	private $rule;
	private $files = [];
	private $name;
	static private $upload;

	private function __construct(){}

	static function init($path = NULL, array $extension = []){
		if (!isset(self::$upload)) self::$upload = new Upload;
		return self::$upload->setPath($path)->setExtension($extension);
	}

	function setPath($path){
		if (is_string($path)) $this->path = $path;
		return $this;
	}

	function setExtension(array $extension){
		if (!empty($extension)) $this->extension = $extension;
		return $this;
	}

	function setRule(array $rule){
		$this->rule = $rule;
		return $this;
	}

	function setFile(array $file){
		$this->files[] = $file;
		return $this;
	}

	function setFiles(array $files){
		$temp = [];
		foreach ($files as $file) if (is_array($file)) $temp[] = $file;

		$this->files = $temp;
		return $this;
	}

	function getName(){
		return $this->name;
	}

	function addFiles(array $files){
		foreach ($files as $file) if (is_array($file)) $this->setFile($file);
	}

	function isValid(array $file = NULL){
		if (isset($this->rule)){
			$flag 	= $reset = false;
			$files 	= [];

			if ($file == NULL){
				$reset = true;
				$files = $this->files;
			}elseif (!empty($file)) $files[] = $file;

			if (!empty($files)){
				$temp 		= [];
				$flag 		= true;
				$validate 	= new Validate($this->rule, array_keys($this->rule));

				do{
					$file = array_shift($files);
					$validate->setSource($file)->run();
					$flag = ($flag && $validate->isFullValid());
					$temp[] = $file;
				}while(!empty($files));

				if ($reset) $this->files = $temp;
			}

			return $flag;
		}
		return true;
	}

	function action($rename = true, $newName = NULL, $changeName = true){
		$result = false;

		if (isset($this->path)){
			$path 	= K_ROOT . $this->path;
			$status = !File::isDir($path) ? $this->createDir($this->path) : true;

			if ($status && !empty($this->files)){
				$file = array_shift($this->files);

				if ($this->isValid($file) && isset($file['name']) && isset($file['tmp_name'])){
					$name = $file['name'];

					if ($rename === true){
						if (is_string($newName)) $name = $newName;
						else{
							$extension 	= strtolower(pathinfo($name, PATHINFO_EXTENSION));
							$name 		= time() . '_' . (new Token)->generate(8) . '.' . $extension;
						}
					}

					if ($changeName === true){
						$extension 	= strtolower(pathinfo($name, PATHINFO_EXTENSION));
						$oldName 	= preg_replace('/\.' . $extension . '$/', '', $name);
						
						$temp 	= $path . $name;
						$i 		= 1;

						while (File::isFile($temp)){
							$name = "{$oldName}({$i}).{$extension}";
							$temp = $path . $name;
							++$i;
						}
					}

					$this->name = $name;
					$filePath = K_ROOT . $this->path . K_DS . $name;

					@move_uploaded_file($file['tmp_name'], $filePath);
					$result = File::isFile($filePath);
				}
			}
		}

		return $result;
	}

	private function createDir($dir){
		$status = true;

		$strPath = str_replace(K_DS, '/', $dir);
		$strPath = preg_replace('/\/+/', '/', $strPath);

		$arrPath = explode('/', $strPath);
		$current = K_ROOT;

		foreach ($arrPath as $name){
			$current .= $name . K_DS;
			$status = File::mkDir($current);
			if (!$status) break;
		}

		return $status;
	}

}