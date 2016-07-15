<?php

class Email_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initAuloload()
	{
		$resourceLoader = $this->_resourceAuloloader;
	}
}