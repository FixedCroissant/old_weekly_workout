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

			//Get WorkoutInformation
            $this->getViewData($unityid);
			
			// set the layout to include the admin links if role=admin
			if ($role == 'admin')
			{
				$this->_helper->layout->setLayout('admin');
			}

			// put the name of the person logged in at the top of the page
			$this->view->pageHeader = $identity->firstname
										. " " . $identity->lastname;
		}
		else
		{
				$this->_redirect('/login/index');
		}
    }

	public function indexAction()
	{


	}

	public function deleteAction()
	{
		$workoutModel = new Model_WorkoutData();
		$id = $this->_request->getParam('id');
		$workoutModel->deleteWorkout($id);
		return $this->_redirect('/index');
	}

	/**
     * Pull additional information needed for the view when reloading the index/index file.
     * @param unityID
     */

	private function getViewData($unityID){

        // get the date ranges the app will be active
        // instantiate the view objects
        $datesModel = new Model_WorkoutDates();
        $numberOfWeeks = $datesModel->getNumberOfWeeks();
        $startDate = $datesModel->getStartingDate(1);
        //Get the week's ending date.
        $getCurrentWeek = $datesModel->getCurrentWeek($startDate);
        //Get the week's ending date -- what is returned is an array in the above $getCurrentWeek, must access the key
        //to get the correct number value, when setting the endDate.
        $endDate = $datesModel->getEndingDate($getCurrentWeek['week']);
        $start = new Zend_Date($startDate, Zend_Date::TIMESTAMP);
        $end = new Zend_Date($endDate, Zend_Date::TIMESTAMP);
        $now = new Zend_Date();
        $current = new Zend_Date($now, Zend_Date::TIMESTAMP);
        $this->view->current = $current->get(Zend_Date::DATE_LONG);
        //Get Workout Data.
        $workoutData = new Model_WorkoutData();
        $weeklyWorkoutData = $workoutData->getThisWeeksWorkouts($unityID,$startDate,$endDate);

        $ns2 = new Zend_Session_Namespace('HeartrateData');
        $ns2->array = array();
        $tmp2 = $ns2->array;

        $hrdata = new Model_HeartrateData();
        //Get logged in user's HR data (if any).
        $row = $hrdata->getHeartrateData($unityID);

        foreach ($row as $key => $value)
        {
            //echo $value;
            $tmp2[$key] = $value;
        }
        $ns2->array = $tmp2;
        //Information for the view.

        //Reset values for the view.
        $this->view->start = $start;
        $this->view->end = $end;
        $this->view->ns2 = $ns2;
        //return var_dump($weeklyWorkoutData);
        $this->view->thisWeeksWorkouts = $weeklyWorkoutData;
    }

}
