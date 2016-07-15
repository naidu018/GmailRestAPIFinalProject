<?php

class Email_ContactsController extends Zend_Controller_Action
{
	public function init()
	{
		Bugs_Auth_Session::check();
		
		$this->_helper->ajaxContext()
			->addActionContext('get-from-bbe', 'json')
			->addActionContext('get-cache', 'json')
			->initContext();
	}
	
	public function getFromBbeAction()
	{
		$url = Bugs_Auth_Session::getIdentity()->bbe_base_url . "services/contact/get-all/";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_URL, $url);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
//			"email" => $email,
//			"psetups" => $psetups
//		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		curl_close ($ch);
		
		echo $result;
		exit;
	}
	
	public function getCacheAction()
	{
		echo json_encode(array("donald", "duck"));
		exit;
	}
	
}