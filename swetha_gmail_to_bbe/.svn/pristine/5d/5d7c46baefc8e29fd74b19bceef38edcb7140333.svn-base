<?php

class Bugs_Auth_Session
{
	static protected $_namespace;
	
	protected static function _getSessionNamespace()
	{
		if(is_null(self::$_namespace))
		{
			self::$_namespace = new Zend_Session_Namespace("bbe_bugs_user");
		}
		
		return self::$_namespace;
	}
	
	public static function check()
	{
		if(self::hasIdentity() && isset($_REQUEST["p"]))
		{
			$token_info = str_replace(" ", "+", $_REQUEST["p"]);
			$token_info = Bugs_Common_Utils_Crypt::decrypt($token_info, md5("bbe_TO_bbe_bugs"));
			
			if(isset($token_info["email"]) && ($token_info["email"] != self::getIdentity()->email))
			{
				self::clearIdentity();
			}
		}
		
		if(!self::hasIdentity())
		{
			if(isset($_REQUEST["p"]))
			{
				// http://localhost:8888/nmum/public/email/bugs/index#
				
				$token_info = str_replace(" ", "+", $_REQUEST["p"]);
				$token_info = Bugs_Common_Utils_Crypt::decrypt($token_info, md5("bbe_TO_bbe_bugs"));
				
				if($token_info && isset($token_info["base_url"]) && isset($token_info["email"]) && isset($token_info["token"]))
				{
					$base_url = parse_url($token_info["base_url"]);
					$base_url = $base_url["path"];
					
					$config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
					
					$remote_bbe_urls = $config->getOption('remote_bbe_url');
					
					if(isset($token_info["application_env"]) && isset($remote_bbe_urls[$token_info["application_env"]]))
					{
						$base_url = $remote_bbe_urls[$token_info["application_env"]] . $base_url;
					}
					
					$email = $token_info["email"];
					$nickname = (isset($token_info["nickname"]) && $token_info["nickname"]) ? $token_info["nickname"] : $email;
					$timezone = (isset($token_info["timezone"]) && $token_info["timezone"]) ? $token_info["timezone"] : "GMT";
					$token = $token_info["token"];
					$valid_tester = md5(strrev($token) . "_token_salt_" . strrev($email));
					
					$url = $base_url . str_replace("//", "/", "/auth/token/get/email/{$email}/token/{$token}/");
					
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
					curl_setopt($ch, CURLOPT_TIMEOUT, 3);
					$result = curl_exec($ch);
					curl_close($ch);
					$result = json_decode($result);
					
					if($result && isset($result->success) && $result->success
						&& isset($result->valid) && ($result->valid == $valid_tester) 
					){
						$session_OBJ = self::_getSessionNamespace();
						$session_OBJ->email = $email;
						$session_OBJ->nickname = $nickname;
						$session_OBJ->timezone = $timezone;
						$session_OBJ->is_system_admin = (isset($result->is_system_admin) && $result->is_system_admin) ? $result->is_system_admin : 0;
						$session_OBJ->has_Tecnavia_system_group = (isset($result->has_Tecnavia_system_group) && $result->has_Tecnavia_system_group) ? $result->has_Tecnavia_system_group : 0;
						$session_OBJ->bbe_base_url = $base_url;
					}
				}
			}
		}
		if(!self::hasIdentity())
		{
			echo "Go to login";
			exit;
		}
	}
	
	public static function getIdentity()
	{
		return self::_getSessionNamespace();
	}
	
	public static function hasIdentity()
	{
		$session_OBJ = self::_getSessionNamespace();
		
		return (isset($session_OBJ->email) && $session_OBJ->email);
	}
	
	public static function getTimezone()
	{
		$session_OBJ = self::_getSessionNamespace();
		
		return (isset($session_OBJ->timezone) && $session_OBJ->timezone) ? $session_OBJ->timezone : "GMT";
	}
	
	public static function clearIdentity()
	{
		self::_getSessionNamespace()->unsetAll();
	}
}