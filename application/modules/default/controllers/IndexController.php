<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
		$this->view->headTitle('Main Menu');
    }


    public function preDispatch()
    {
        // Get the Zend_Auth reference
		$auth = Zend_Auth::getInstance();

		// Check to see if the user is logged in
		if ($auth->hasIdentity())
		{
			//Get the user's identity and set the view variable
			$identity = $auth->getIdentity();
			$unityid = $identity->unityid;
			$role = $identity->role;
			
			// set the layout to include the admin links if role=admin
			if ($role == 'admin') 
			{
				$this->_helper->layout->setLayout('admin');
			}

			// put the name of the person logged in at the top of the page
			//$this->view->pageHeader = $identity->firstname
			//							. " " . $identity->lastname;
		}
		else
		{
				$this->_redirect('/login/index');
		}
    }
	
	public function indexAction()
	{
		// get the controller name
		$fc = Zend_Controller_Front::getInstance();
		$controller = $fc->getRequest()->getControllerName();
		$this->view->controller = $controller;

		if (Zend_Session::namespaceIsset('HeartrateData'))
		{
			$hrdata = new Zend_Session_Namespace('HeartrateData');
			$this->view->hrdata = $hrdata;
		}
		else
		{
			$this->_helper->redirector('add', 'calculator');
		}

		$auth = Zend_Auth::getInstance();
		$unityid = $auth->getIdentity()->unityid;

		// get the current date and it's timestamp 
		$now     = new Zend_Date();
		$current = $now->get(Zend_Date::TIMESTAMP);

		// get the workout date ranges from the database
		$dates     = new Model_WorkoutDates();
		$workoutModel = new Model_WorkoutData();

		// search the date ranges for the current week
		$weekArray = $dates->getCurrentWeek($current);
		$weekStart = $weekArray['start'];
		$weekEnd = $weekArray['end'];

		if (!$weekArray['start'] || !$weekArray['end'])
		{
			return $this->render('index');	
		}
		else
		{
			/**
			 * Create a view object to display the range of dates
			 * encompassed in the current week.
			 */
			$startDate = new Zend_Date($weekStart, Zend_Date::TIMESTAMP);
			$this->view->start = $startDate->get(Zend_Date::DATE_LONG);
			$endDate = new Zend_Date($weekEnd, Zend_Date::TIMESTAMP);
			$this->view->end = $endDate->get(Zend_Date::DATE_LONG);


			// get the workouts that occurs during the current week
			$thisWeekWorkouts = 
				$workoutModel->getThisWeeksWorkouts($unityid, $weekStart, $weekEnd);
			$this->view->thisWeeksWorkouts = $thisWeekWorkouts;
		}

	}

	public function deleteAction()
	{
		$workoutModel = new Model_WorkoutData();
		$id = $this->_request->getParam('id');
		$workoutModel->deleteWorkout($id);
		return $this->_redirect('/index');
	}


}
