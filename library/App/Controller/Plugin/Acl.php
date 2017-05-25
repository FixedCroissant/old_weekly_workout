<?php
class App_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// set up acl
		$acl = new Zend_Acl();

		// add roles
		$acl->addRole(new Zend_Acl_Role('student'));
		$acl->addRole(new Zend_Acl_Role('admin'),'student');


		$acl->add(new Zend_Acl_Resource('calculator'));
		$acl->add(new Zend_Acl_Resource('index'));
		$acl->add(new Zend_Acl_Resource('login'));
		$acl->add(new Zend_Acl_Resource('user'));
		$acl->add(new Zend_Acl_Resource('workout'));
		$acl->add(new Zend_Acl_Resource('dates'));
		$acl->allow('student','index')
			->allow('student','login')
			->allow('student','user')
			->allow('student','calculator')
			->allow('student','workout');

		$acl->add(new Zend_Acl_Resource('admin'));
		$acl->add(new Zend_Acl_Resource('admin:workouts','admin'));
		$acl->add(new Zend_Acl_Resource('admin:dates', 'admin'));
		$acl->add(new Zend_Acl_Resource('admin:user','admin'));
		$acl->add(new Zend_Acl_Resource('admin:grades','admin'));
		
		$acl->allow('admin');

			// fetch the current user
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$identity = $auth->getIdentity();
			$role = $identity->role;
		}
		else
		{
			$role = 'student';
		}
	
		if ($request->module == 'admin')
		{
			$controller = $request->module . ":" . $request->controller;
		}
		else
		{
			$controller = $request->controller;
		}

		$action = $request->action;

		if(!$acl->has($controller,$action)) 
		{
	
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('error');
			return;
		}
		
		if(!$acl->isAllowed($role,$controller,$action))
		{
			$request->setModuleName('default');
			$request->setControllerName('error');
			$request->setActionName('noauth');
		}
	}
}
