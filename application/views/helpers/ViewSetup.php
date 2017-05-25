<?php

class Zend_View_Helper_ViewSetup extends Zend_View_Helper_Abstract
{
    public function _initView()
    {
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers');

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        return $view;
    }
}


