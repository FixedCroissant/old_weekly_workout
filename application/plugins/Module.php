<?php

class Plugin_Module extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
		$controller = $request->getControllerName();

		//var_dump($controller);
        $front_controller = Zend_Controller_Front::getInstance();

        //var_dump($front_controller);

        $error_handler = $front_controller->getPlugin('Zend_Controller_Plugin_ErrorHandler');



        $error_handler->setErrorHandlerModule($module);

		// check the module and automatically set the layout

		$layout = Zend_Layout::getMvcInstance();

		switch ($module) {
			case 'admin':
                $layout->setLayout('admin');
                break;

            default:
                $layout->setLayout('default');
				break;
		}
    }
}
