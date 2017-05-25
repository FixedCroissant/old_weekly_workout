<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->_helper->layout->setLayout('login');
		$this->view->headTitle('Login');

    }

	public function getForm() 
	{
		return new Form_Login(array(
			'action' => 'index',
			'method' => 'post'
			));
	}

	public function _getAuthAdapter($unityid, $password)
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		$authAdapter->setTableName('users')
					->setIdentityColumn('unityid')
					->setCredentialColumn('password')
					->setCredentialTreatment('sha1(?)');
		$registry = Zend_Registry::getInstance();
		$salt = $registry->config->salt;
		$password = $salt.$password;
		$authAdapter->setIdentity($unityid)
					->setCredential($password);
		return $authAdapter;
	}

	public function indexAction()
	{
		// get the date ranges the app will be active
		// instantiate the view objects
		$datesModel = new Model_WorkoutDates();
		$numberOfWeeks = $datesModel->getNumberOfWeeks();
		$startDate = $datesModel->getStartingDate(1);
		$endDate = $datesModel->getEndingDate($numberOfWeeks);
		$start = new Zend_Date($startDate, Zend_Date::TIMESTAMP);
		$end = new Zend_Date($endDate, Zend_Date::TIMESTAMP);
		$now = new Zend_Date();
		$current = new Zend_Date($now, Zend_Date::TIMESTAMP);
		$this->view->current = $current->get(Zend_Date::DATE_LONG);

		if ($current < $start || $current > $end)
		{
			$this->view->start = $start->get(Zend_Date::DATE_LONG);
			$this->view->end = $end->get(Zend_Date::DATE_LONG);
		}

		$form = $this->getForm();
		$this->view->form = $form;
		$request = $this->getRequest();


		if ($request->isPost())
		{
			$data = $form->getValues();
		
			if (!$form->isValid($_POST))
			{
				$form->populate($data);
				return $this->render('index');
			}
			
			$unityid = $form->getValue('unityid');
			$password = $form->getValue('password');

			// authentication process
			$authAdapter = $this->_getAuthAdapter($unityid, $password);

			// authenticate the user
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($authAdapter);
			
			if ($result->isValid())
			{
				$storage = $auth->getStorage();
				$storage->write($authAdapter->getResultRowObject(array(
									'unityid',
									'firstname',
									'lastname',
									'email',
									'role'
									)));
				
				


				$hrdata = new Model_HeartrateData();
				$row = $hrdata->getHeartrateData($unityid);
				
				
				// If heartrate data exists
				if ($row)
				{
				/**
				 * Use the following workaround to solve the issue of storing an array of data in the Zend Session
				 * component for use in other areas of the application. First, create the Zend Session Namespace,
				 * then create the array, and finally iterate through the array data that you'd like to store.
				 * The trick is to store the array data in a temporary array then assign that array to the 
				 * namespace array.
				 */

					$ns2 = new Zend_Session_Namespace('HeartrateData');
					$ns2->array = array();
					$tmp2 = $ns2->array;
					foreach ($row as $key => $value)
					{
						$tmp2[$key] = $value;
					}
					$ns2->array = $tmp2;

					return $this->_helper->redirector('index', 'index');
				}
				// if no heartrate data exists
				else
				{
					return $this->_helper->redirector('add','calculator');

				}
			}
			else
			{
				// invalid credentials
				$form->populate(array('unityid' => $unityid));
				$this->view->formError = 'Invalid username and/or password';
				return $this->render('index');
			}
		
		}

		$this->view->form = $form;
	}

	public function logoutAction()
	{
		Zend_Session::destroy(true,false);
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index'); // back to login page
	}

}
