<?php
namespace Application;
defined('K_ROOT') || die(pathinfo(__FILE__, PATHINFO_FILENAME));

use Library\File;
use Library\Json;

final class View{

	private static $view;
	private $template;

	private function __construct(){}

	static function init(){
		if (!isset(self::$view)) self::$view = new View();
		return self::$view;
	}

	function render($request){
		$this->initModule($this->getModule($request));
		$dirURL = $request->getUrl()->dir;

		try{
			$post 	= $request->getPost();
			$get 	= $request->getGet();
			$files 	= $request->getFiles();

			if ($this->loadModuleConfig()->configLoadIndex()){
				$moduleIndex = join(K_DS, [$this->modulePath, 'index.php']);
				if (!File::isFile($moduleIndex))
					throw new ApplicationException("Cannot found Module Index.", 0, 0, __FILE__, __LINE__);
				include $moduleIndex;
			}

			if (isset($this->useTheme) && true === $this->useTheme){
				$this->initTheme();
				$indexTheme = join(K_DS, [$this->themePath, 'index.php']);

				if (!File::isFile($indexTheme)) 
					throw new ApplicationException("Cannot found Theme Index.", 0, 0, __FILE__, __LINE__);

				$config = $this->loadThemeConfig();

				$primary = 'Theme';
				if (isset($config->primary)){
					$cPrimary = $config->primary;
					if (is_string($cPrimary)) $primary = ucfirst($cPrimary);
				}

				$folder = 'Html';
				if (isset($config->folder)){
					$cFolder = $config->folder;
					if (is_string($cFolder)) $folder = ucfirst($cFolder);
				}

				$extension = 'html';
				if (isset($config->extension)){
					$cExtension = $config->extension;
					if (is_string($cExtension)) $extension = strtolower($cExtension);
				}

				$header = 'text/html';
				if (isset($config->header)){
					$cHeader = $config->header;
					if (is_string($cHeader)) $header = $cHeader;
				}

				$this->template = Template::init();
				$this->template->setFolder(join(K_DS, [$this->themePath, $folder]));
				$this->template->setExtension($extension)->setHeader($header);

				include $indexTheme;
			}

			$rewrite 	= $this->configRewrite();
			$isFirstDir = $this->configIsFirstDir();
			$module 	= $this->getModuleDefault();

			if ($isFirstDir){
				if (isset($dirURL[0])) $module = $dirURL[0];
				if ($this->configRAU()){
					$arr 	= explode('-', trim($module, '-'));
					$module = '';
					foreach ($arr as $value) $module .= ucfirst($value);
				}
			}

			if (!$rewrite){
				$arrDefault = $this->configDefaultGet();
				if (isset($arrDefault[0])) $module = trim(Json::encode($arrDefault[0]), '"');
			}
			
			$moduleFooter = join(K_DS, [$this->modulePath, 'footer.php']);
			if (!File::isFile($moduleFooter))
				throw new ApplicationException("Cannot found Module Footer.", 0, 0, __FILE__, __LINE__);
			include $moduleFooter;

			if (isset($this->useTheme) && true === $this->useTheme){
				$pathTheme = join(K_DS, [$this->themePath, $folder, $primary . '.' . $extension]);
				if (!File::isFile($pathTheme))
					throw new ApplicationException("Cannot found Primary Theme File.", 0, 0, __FILE__, __LINE__);

				$this->template->setTheme($pathTheme);
				new Response('Content-Type: ' . $this->template->getHeader(), function(){
					return $this->template->getContent();
				});
				exit;
			}
		}catch(ApplicationException $error){
			prr($error);
		}
	}

	private function getModuleDefault(){
		$result = 'home';
		if (isset($this->config->moduleDefault))
			$result = trim(Json::encode($this->config->moduleDefault), '"');
		return $result;
	}

	private function getActionDefault(){
		$result = 'home';
		if (isset($this->config->actionDefault))
			$result = trim(Json::encode($this->config->actionDefault), '"');
		return $result;
	}

	private function getModule($request){
		$requestURL = $request->getUrl();
		$dirURL		= $requestURL->dir;
		$module 	= K_DIR_PUB;

		if (isset($dirURL[0])){
			$dir 	= strtolower($dirURL[0]);
			$dash 	= strtolower(K_URL_DASH);
			$other 	= [$dash, strtolower(K_URL_REST)];

			if (in_array($dir, $other)){
				$inURL 	= strtolower(array_shift($dirURL));
				$module = ($inURL == $dash) ? K_DIR_DASH : K_DIR_REST;
				$requestURL->dir = $dirURL;
			}
		}

		return $module;
	}

	private function initModule($module){
		$moduleDir	= join(K_DS, [K_ROOT . K_DIR_MODULE, ucfirst($module)]);
		File::mkDir($moduleDir);

		$moduleName 	= 'Default';
		$moduleSetting	= join(K_DS, [$moduleDir, 'Setting.module']);

		if (File::exist($moduleSetting)){
			$setting = Json::loadFromFile($moduleSetting);

			if (is_object($setting) && isset($setting->module_name)){
				$name = $setting->module_name;
				if (!!$name && is_string($name)) $moduleName = $name;
				if (isset($setting->use_theme) && true === $setting->use_theme){
					$this->useTheme = true;
					$this->theme = (isset($setting->theme) && !!$setting->theme && is_string($setting->theme)) ? 
					$setting->theme : 'Default';
				}
			}
		}else{
			$setting 	= Json::encode(['module_name' => $moduleName, 'use_theme' => false]);
			$handle 	= File::open($moduleSetting, 'w');
			if ($handle) File::write($handle, $setting);
		}

		$modulePath = join(K_DS, [$moduleDir, ucfirst($moduleName)]);
		File::mkDir($modulePath);

		$this->modulePath 	= $modulePath;
		$this->module 		= $module;
	}

	private function initTheme(){
		$module 	= isset($this->module) ? $this->module : K_DIR_PUB;
		$themeDir	= join(K_DS, [K_ROOT . K_DIR_THEME, $module]);
		File::mkDir($themeDir);

		$themeName	= isset($this->theme) ? $this->theme : 'Default';
		$themePath 	= join(K_DS, [$themeDir, ucfirst($themeName)]);
		File::mkDir($themePath);
		
		$this->themePath = $themePath;
	}

	private function loadModuleConfig(){
		$moduleConfig = join(K_DS, [$this->modulePath, 'Module.config']);
		if (!File::exist($moduleConfig))
			throw new ApplicationException("Cannot found Module Config.", 0, 0, __FILE__, __LINE__);

		$config = Json::loadFromFile($moduleConfig);
		if (!is_object($config))
			throw new ApplicationException("Module Config invalid.", 0, 0, __FILE__, __LINE__);

		$this->config = $config;
		return $this;
	}

	private function loadThemeConfig(){
		$configPath = join(K_DS, [$this->themePath, 'Theme.config']);
		if (!File::exist($configPath))
			throw new ApplicationException("Cannot found Theme Config.", 0, 0, __FILE__, __LINE__);

		$config = Json::loadFromFile($configPath);
		if (!is_object($config))
			throw new ApplicationException("Theme Config invalid.", 0, 0, __FILE__, __LINE__);

		return $config;
	}

	private function configLoadIndex(){
		$result = true;
		if (isset($this->config->loadIndex)){
			$loadIndex = $this->config->loadIndex;
			if (is_bool($loadIndex)) $result = $loadIndex;
		}
		return $result;
	}

	private function configRewrite(){
		$result = true;
		if (isset($this->config->rewrite)){
			$rewrite = $this->config->rewrite;
			if (is_bool($rewrite)) $result = $rewrite;
		}
		return $result;
	}

	private function configIsFirstDir(){
		$result = true;
		if (isset($this->config->isFirstDir)){
			$isFirstDir = $this->config->isFirstDir;
			if (is_bool($isFirstDir)) $result = $isFirstDir;
		}
		return $result;
	}

	private function configRAU(){
		$result = true;
		if (isset($this->config->replaceAndUcfirst)){
			$rau = $this->config->replaceAndUcfirst;
			if (is_bool($rau)) $result = $rau;
		}
		return $result;
	}

	private function configDefaultGet(){
		$result = [];
		if (isset($this->config->getName)){
			$getName = $this->config->getName;
			if (is_array($getName)) $result = array_values($getName);
		}
		return $result;
	}
}