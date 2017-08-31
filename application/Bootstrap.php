<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

		public function __construct($application)
    {
        parent::__construct($application);        
    }

    public function run()
    {
        parent::run();
    }

    protected function _initAutoload()
    {
			$autoLoader = Zend_Loader_Autoloader::getInstance();
      $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath'  => APPLICATION_PATH));
        
			$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
				'basePath'      => APPLICATION_PATH,
				'namespace'     => '',
				'resourceTypes' => array(
					'form'  	=> array('path' => 'forms/', 
									'namespace' => 'Form_'),
					'model'     => array('path' => 'models/', 
									'namespace' => 'Model_')
				)
			));
        return $autoLoader;
    }

    protected function _initUrl()
    {
        $baseUrl = substr($_SERVER['PHP_SELF'], 0,
        		strpos($_SERVER['PHP_SELF'], '/public/index.php'));
        
        $zcf = Zend_Controller_Front::getInstance();
        
        $zcf->setBaseUrl($baseUrl);
        
        $s = empty($_SERVER["HTTPS"]) ? '' 
					: ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $protocol = substr(
            strtolower($_SERVER["SERVER_PROTOCOL"]),
            0,
            strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")
        ) . $s;
        $port = ($_SERVER["SERVER_PORT"] == "80") ? ""
					: (":".$_SERVER["SERVER_PORT"]);
        $url = $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $baseUrl;
        
        Zend_Registry::set('siteUrl', $url);
    }

    public function _initTranslate()
    {
        // this will make App_Translate use the right language
        $translate = new Zend_Translate('array', APPLICATION_PATH .
        '/languages/en.php', 'en');
        Zend_Registry::set('Zend_Translate', $translate);
        return $translate;
    }


    protected function _initView()
    {
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');

        $view->addHelperPath(APPLICATION_PATH . '/views/helpers');

        $viewRenderer = Zend_Controller_Action_HelperBroker
								::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);

        return $view;
    }

/**
 *  This function will load the config file so it is available for use
 *  throughout the web application.
 */
	
    protected function _initConfig()
    {
        $options = $this->getOptions();
        $config = new Zend_Config($options);
        Zend_Registry::set('config', $config);
        return $config;
    }

    /*protected function _initPlugins()
		{
			$zcf = Zend_Controller_Front::getInstance();

			// register the authenticator plugin to handle login authentication
			$authenticator = new Plugin_Authenticator();
			$zcf->registerPlugin($authenticator);

			// register the Access Control List plugin to protect the admin areas
			$access = new App_Controller_Plugin_Acl($acl);
			$zcf->registerPlugin($access,'student');

			// register the module plugin to change layout depending upon active module
			$module = new Plugin_Module();
			$zcf->registerPlugin($module);
		}

	*/
}
