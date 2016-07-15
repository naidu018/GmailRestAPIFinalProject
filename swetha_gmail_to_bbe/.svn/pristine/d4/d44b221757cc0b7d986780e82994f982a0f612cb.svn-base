<?php

class Email_MailCountController extends Zend_Controller_Action
{
	public function init()
	{
	}
	
	public function getUnreadAction()
	{
		$statistics = Email_Model_Service_ShowMails::countUnread();
		
		echo json_encode(array(
			"success" => 1,
			"count" => $statistics["count"],
			"oldest_mail_time" => $statistics["oldest_mail_time"],
		));
		exit;
	}
	
	
}