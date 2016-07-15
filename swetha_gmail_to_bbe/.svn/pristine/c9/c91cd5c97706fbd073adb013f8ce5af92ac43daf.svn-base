<?php

class Pubb_AjaxController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->ajaxContext()
			->addActionContext('get-all', 'json')
			->initContext();
	}
	
	public function getAllAction()
	{
		$email_from = $this->_getParam("email_from");
		echo Bugs_Pubb_Service::getAll_json($email_from);
		exit;
	}
}