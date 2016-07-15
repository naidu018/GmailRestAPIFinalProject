<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	function _initConstants()
	{
		if($this->getOption('custom_constants'))
		{
			foreach ($this->getOption('custom_constants') as $k => $v) {
				define($k, $v);
			}
		}
		
		$request_uri_ARRAY = explode("/public/", $_SERVER["REQUEST_URI"]);
		
		$port = ($_SERVER["SERVER_PORT"] != "80") ? ":" . $_SERVER["SERVER_PORT"] : "";
		
		if(defined("SERVER_PORT"))
		{
			$port = ":" . SERVER_PORT;
		}
		
		if(count($request_uri_ARRAY) == 2){
			$base_url = "http://" . $_SERVER["SERVER_NAME"] . $port . $request_uri_ARRAY[0] . "/public/";
		}else{
			$base_url = "http://" . $_SERVER["SERVER_NAME"] . $port . "/";
		}
		
		define("BASE_URL", $base_url);
		define('BASE_PATH', realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'));
		define("SERVER_NAME", "http://" . $_SERVER["SERVER_NAME"]);
		
		$nmum_version = explode("/", APPLICATION_PATH);
		$nmum_version = isset($nmum_version[count($nmum_version) - 2]) ? $nmum_version[count($nmum_version) - 2] : "";
		define("NMUM_VERSION", $nmum_version);
	}
	
//	function _initTranslator()
//	{
//		$locale = new Zend_Locale();
//		$locale->setLocale("en_EN");
//		
//		$translate = new Zend_Translate(
//		    array(
//		        'adapter' => 'array',
//		    	'content' => array("#__#"=>"#__#"),
//		        'locale'  => $locale
//		    )
//		);
//		
//		Zend_Registry::set('Zend_Translate', $translate);
//		
//		$lang = "en";
//		
//		$user_storage = Zend_Auth::getInstance()->getStorage();
//		if(!$user_storage->isEmpty()){
//			$user_storage = $user_storage->read();
//			if(isset($user_storage->lang)){
//				$lang = $user_storage->lang;
//			}
//		}
//		
//		Zend_Registry::set('lang', $lang);
//		
//		Zend_Registry::set('locale', "en_US");
//	}
	

}

