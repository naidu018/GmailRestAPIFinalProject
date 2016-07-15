<?php

class Pubb_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initAuloload()
	{
		$resourceLoader = $this->_resourceAuloloader;
	}
}