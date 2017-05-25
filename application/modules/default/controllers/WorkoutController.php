<?php

class WorkoutController extends Zend_Controller_Action
{

	protected $_flashMessenger = NULL;

	/**
	 * _flashMessage 
	 *
	 *	Sets up the flash messenger to record and publish errors
	 * 
	 * @param mixed $message 
	 * @return void
	 */
	protected function _flashMessage($message) {
		$flashMessenger = $this->_helper->FlashMessenger;
		$flashMessenger->setNamespace('workoutErrors');
		$flashMessenger->addMessage($message);
	}

    /**
     * init
	 *
	 *	Initiate the page variables
     * 
     * @return void
     */
    public function init()
    {
		if (!Zend_Session::namespaceIsset('HeartrateData'))
		{
			$this->_helper->redirector('add', 'calculator');
		}
        /* Initialize action controller here */
    }
    
        
      /**
   * checkValidPostHR
   *
   * 	Check whether the user-supplied Post Heart Rate is normal.
   *    If under 140 bpm, no error shown, however the postHR is greater
   *    than or equal to 140, throw an error on the screen and 
   *    do not add to the database.
   * @param integer postHeartRate
   * @return void
   */
  public function checkValidPostHR($postHR){
	  if($postHR>=140){
			$this->_flashMessage('The Post HeartRate you entered is too high, please try again.');
			$this->_redirect('/workout/add');
			return;
	  }
	  //Do nothing...
	  else{
		  }
  }

	/**
	 * getForm 
	 *
	 *	Returns the add workout form
	 *
	 * @return object
	 */
	public function getForm()
	{
		return new Form_AddWorkout(array(
			'action' => 'add',
			'method' => 'post'
			));
	}

	public function indexAction()
	{
		$this->_redirect('workout/add');
	}
	
    public function addAction()
    {
		$this->view->headTitle('Add Workout');
		$this->view->pageHeader = 'Add Workout';

		$form = $this->getForm();
		$flashMessenger = $this->_helper->FlashMessenger;
		$flashMessenger->setNamespace('workoutErrors');
		$this->view->workoutErrors = $flashMessenger->getMessages();
		$this->view->form = $form;

		if (!$this->getRequest()->isPost())
		{
			return $this->render('add');
		}

		$formData = $this->getRequest()->getPost();
		
		if (!$form->isValid($formData))
		{
			$form->populate($formData);
			$this->view->form = $form;
			return $this->render('add');
		}

		// Here's where the form processing starts
		// so we can insert the workout into the db
		
		// get the unityid of the logged in user
		$auth = Zend_Auth::getInstance();
		$unityid = $auth->getIdentity()->unityid;
		$submittedBy = $unityid;

		// get the proposed workout date object
		$month       = $form->getValue('month');
		$day         = $form->getValue('day');
		$year        = $form->getValue('year');
		$wkdate = new Zend_Date("$month $day $year");
		$postHR 	 = $form->getValue('posthr');
		$workoutDate = $wkdate->get(Zend_Date::TIMESTAMP);
		//$this->_flashMessage("$month $day $year".' The object '.$wkdate.' The timestamp '.$workoutDate);

		$data	= new Model_WorkoutData();

		// check to see if the date is real or a workout already exists for
		// this date
		if (!Zend_Date::isDate($wkdate))
		{
			$this->_flashMessage('The date you entered does not	exist!');	
			$this->_redirect('/workout/add');
			return;
		}
		elseif ($data->workoutExists($unityid, $workoutDate))
		{
			$this->_flashMessage('A workout already exists for the date you	entered!');
			$this->_redirect('/workout/add');
			return;
		}

		// get the current date and it's timestamp 
		$now     = new Zend_Date();
		$current = $now->get(Zend_Date::TIMESTAMP);

		// don't allow users to submit a workout in the future
		if ($wkdate->get(Zend_Date::DAY_OF_YEAR) >
				$now->get(Zend_Date::DAY_OF_YEAR))
		{
			$this->_flashMessage("The date of your workout hasn't occurred yet!");
			$this->_redirect('/workout/add');
			return;
		}


		// get the workout date ranges from the database
		$dates     = new Model_WorkoutDates();

		// search the date ranges for the current week
		$weekRow = $dates->isWorkoutWeekValid($current,$workoutDate);
		$thisWeek = $dates->occursThisWeek($current, $weekRow);
		$week = $weekRow['week'];
		
		// check that workout falls within valid workout weeks
		if ($week)
		{
			$count = $data->getNumberOfWorkouts($unityid, $week);
		}
		else
		{
			$this->_flashmessage('You cannot add workouts yet!');
			$this->_redirect('/workout/add');
			return;
		}

		if ($count >= '3')
		{
			$this->_flashMessage('You have already recorded three workouts for the given week!');
			$this->_redirect('/workout/add');
			return;
		}
		
		//Check the Post HR provided by the user.
		//Start additions on 12-18-2015
			$this->checkValidPostHR($postHR);
		//End additions on 12-18-2015.

		//if ($week && $thisWeek)
		if ($week && $thisWeek && $postHR<140)
		{
			$ns = new Zend_Session_Namespace('HeartrateData');
		
			//assign the class properties
			$hr60 = $ns->array['hr_60'];
			$hr90 = $ns->array['hr_90'];

			$grade = $data->calculateGrade(
							$form->getValue('prehr'),
							$form->getValue('peakhr'),
							$form->getValue('posthr'),
							$form->getValue('pushups'),
							$form->getValue('crunches'),
							$form->getValue('wklength'),
							$hr60,
							$hr90
							);
			$score = $grade['score'];
			$comment = $grade['comment'];
			$result = $data->addWorkout(
							$unityid,
							$workoutDate,
							$week,
							$form->getValue('prehr'),
							$form->getValue('peakhr'),
							$form->getValue('posthr'),
							$form->getValue('pushups'),
							$form->getValue('pushuptype'),
							$form->getValue('crunches'),
							$form->getValue('wktype'),
							$form->getValue('wklength'),
							$score,
							$comment,
							$submittedBy	
							);
			if (!$result) {
				$this->_flashMessage('The workout could not be added to the	database!');
				$this->_redirect('/workout/add');
				return;
			}
		}
		else
		{
			$this->_flashMessage('The date you entered does not occur during the current week!');
			$this->_redirect('/workout/add');
			return;
		}
		
	$this->_redirect('/index');

	}

	public function viewAction()
	{
		$this->view->headTitle('View Workout');
		$this->view->pageHeader = 'View Workout';

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

		// get all the past workout
		$startDate = $dates->getStartingDate(1);
		$workouts = $workoutModel->getAllWorkoutsByDate($unityid,$startDate,$weekStart);
		$this->view->allWorkouts = $workouts;
	}



}

