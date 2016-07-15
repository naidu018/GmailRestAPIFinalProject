<?php

class Email_MailAttachController extends Zend_Controller_Action
{
	const DEADLINE_NEVER = "-1";

	public function init()
	{
		Bugs_Auth_Session::check();

		//		$this->_helper->ajaxContext()
		//			->addActionContext('get-data-table', 'json')
		//			->initContext();
	}

	public function downloadAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$base_filename = $this->_getParam("filename");

		if(APPLICATION_ENV == "devtony")
		{
			$base_nas_url = "localhost:8888";
		}
		else
		{
			$base_nas_url = Bugs_Common_Const_Nas::$nas["o"];
		}

		$tmp_path = Bugs_Common_CurlNas::download_file($base_nas_url, "downloadAttachment", "emailServices", array("hash" => $this->_getParam("hash"), "filename" => $base_filename), $base_filename);

		if(file_exists($tmp_path))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$base_filename);
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($tmp_path));
			readfile($tmp_path);
				
			unlink($tmp_path);
				
			exit;
		}



		// on NAS O => /tauser/www/ee/data/newsmem/_showMails/attachs/2015-01-06_22-25-02_46613558



	}
}