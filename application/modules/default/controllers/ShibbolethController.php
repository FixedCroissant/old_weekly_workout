<?php

class ShibbolethController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        // Local to this controller only; affects all actions,
        // as loaded in init:
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {


    }

}

