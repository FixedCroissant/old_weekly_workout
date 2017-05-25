<?php

class Plugin_Authenticator extends Zend_Controller_Plugin_Abstract
{
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$auth = Zend_Auth::getInstance();

		if (!$auth->hasIdentity())
		{
			$this->_request->setModuleName('default');
			$this->_request->setControllerName('Login');
			$this->_request->setActionName('index');
		}
	}
}
