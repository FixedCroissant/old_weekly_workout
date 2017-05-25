<?php

class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
      $this->_helper->layout->setLayout('error');
      $this->view->headTitle('Error', 'SET');
      $this->view->pageHeader = "Application Error";
    }

    public function errorAction()
    {
      $errors = $this->_getParam('error_handler');
      if ($errors->exception instanceof Exception)
      {
        $this->view->menu = "";
        $this->view->exception = $errors->exception;
			}	


      switch ($errors->type) {
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
          // 404 error -- controller or action not found
          $this->getResponse()->setHttpResponseCode(404);
          $this->view->pageHeader = 'Page not found';
          $this->view->errorType = 404;
        break;
        default:
          $this->view->menu = "";
          $this->view->exception = $errors->exception;
                
          //I want to log the errors to my own log file
          $exception = $errors->exception;
          if(APPLICATION_ENV == 'development') 
          {
            $msg = $exception->getMessage();
            $trace = $exception->getTraceAsString();
            echo "<div>Error: $msg<p><pre>$trace</pre></p></div>";
          } 
          else 
          {
            try 
            {
              $registry = Zend_Registry::getInstance();
              $logFile = $registry->config->logFiles->error;
              $log = new Zend_Log(new Zend_Log_Writer_Stream($logFile));
              $log->debug($exception->getMessage() . "\n" 
                 . $exception->getTraceAsString() 
                 . "\n-----------------------------");
            }
            catch (Exception $e) 
            {
              // can't log it - display error message
              die("<p>An error occurred which could not be logged!</p>");
            }
          }
        break;
        }
        
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }

	public function noauthAction()
	{
		$this->view->pageHeader = "Access Denied";
    $this->getResponse()->setHttpResponseCode(401);
	}

}

