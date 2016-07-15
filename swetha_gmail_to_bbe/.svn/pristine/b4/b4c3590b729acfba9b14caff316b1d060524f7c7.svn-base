<?php

class Email_ManageController extends Zend_Controller_Action
{
	public function init()
	{
		Bugs_Auth_Session::check();
	}
	
	public function listAction()
	{
		$this->view->autoload_email_id = $this->_getParam("email_id");
		$this->view->page_title = "History Mails Status";
	}
	
	
}