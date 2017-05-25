<?php

class Admin_DatesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle('Workout Dates');
		$this->view->pageHeader = 'Workout Dates';
    }

	public function getForm()
	{
		return new Form_WorkoutDates(array(
			'action'	=> 'modify',
			'method'	=> 'post'
			));
	}

	public function indexAction()
	{
		$this->_redirect('admin/dates/modify');
	}

    public function modifyAction()
    {
		$form = $this->getForm();
		$this->view->form = $form;

		// use the model to get a list of dates if present
		$workoutDates = new Model_WorkoutDates();
		$currentDates = $workoutDates->getWorkoutDates();
		
		// if the dates are there send them to the view
		if ($currentDates)
		{
			$this->view->range = $currentDates;
		}
		
		if (!$this->getRequest()->isPost())
		{
			return $this->render('modify');
		}

		$formData = $this->getRequest()->getPost();

		if (!$form->isValid($formData))
		{
			$form->populate($formData);
			$this->view->form = $form;
			return $this->render('modify');
		}
		
		// get the form variables
		$start_month = $form->getValue('month');
		$start_day   = $form->getValue('day');
		$start_year  = $form->getValue('year');
		$weeks       = $form->getValue('number_weeks');

		$workoutDates->deleteWorkoutDates();	
		$dateRange = $workoutDates->insertWorkoutDates(
						$start_month, 
						$start_day, 
						$start_year, 
						$weeks
						); 
	
		$currentDates = $workoutDates->getWorkoutDates();
		$this->view->range = $currentDates;
		
		return $this->render('modify');
    }


}

