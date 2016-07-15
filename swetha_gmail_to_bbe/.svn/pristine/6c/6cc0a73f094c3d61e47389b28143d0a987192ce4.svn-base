<?php

class Auth_AuthController extends Zend_Controller_Action
{

	public function init()
	{
//		$ajaxContext = $this->_helper->getHelper('AjaxContext');
//		$ajaxContext->addActionContext('forgot-password-ajax', 'json')
//		->initContext();
	}
	
	public function loginAction()
	{
//		$encrypt = Bugs_Common_Utils_Crypt::encrypt(array(
//			"token" => md5("mytoken"),
//			"email" => "aoppedisano@tecnavia.com",
//			"srv_id" => 5,
//		), "mykey");
//		
//		
//		
//		Zend_Debug::dump($encrypt);
//		
//		Zend_Debug::dump(Bugs_Common_Utils_Crypt::decrypt($encrypt, "mykey"));
//		
//		exit;
	}
	
	public function logoutAction()
	{
		Bugs_Auth_Session::clearIdentity();
		
		echo json_encode(array("success"=>1));
		
		exit;
	}
}

