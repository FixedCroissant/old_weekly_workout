<?php

class CalculatorController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
		$this->view->headTitle('Heartrate Calculator');
		$this->view->pageHeader = 'Heartrate Calculator';
    }

	public function getForm()
	{
		return new Form_Calculator(array(
			'action' => 'add',
			'method' => 'post'
			));
	}

	public function indexAction()
	{
		$this->_redirect('calculator/add');
	}


/**
*	The add action renders the original form and handles the form when
*	there are validation errors present.
*/
	public function addAction()
	{
		$form = $this->getForm();
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

		// get the form values and assign them to variables
		$age = $form->getValue('age');
		$rhr = $form->getValue('rhr');
		$sex = $form->getValue('sex');

		// create an object to calculate the required heart rate counts
		// based on the required level of intensity and store the values
		// in an array so that they can be rendered to the web page
		$hr_values = new App_Heartrate_Calculator($age, $rhr, $sex);
		$hr_range = $hr_values->calculateHeartrateRange();
		$this->view->values = $hr_range;

		// additional information that is not currently used but may
		// be used in the future to illustrate how the heart rate counts
		// are calculated using the Karvonen Formula
		$hrr    = $hr_values->getHeartrateReserve();

		$this->view->age	= $age;
		$this->view->rhr	= $rhr;

		$auth    = Zend_Auth::getInstance();
		$unityid = $auth->getIdentity()->unityid;
		$userid  = $unityid;

		$dataModel = new Model_HeartrateData();
		$dataModel->addHeartrateData($unityid,$age,$sex,$rhr,$hr_range,$userid);
		$row = $dataModel->getHeartrateData($unityid);

		$this->view->row = $row;
		if (!$row)
		{
			$this->view->message = "Your heartrate data could not be added to the database!";
		}
		else
		{
			Zend_Session::namespaceUnset('HeartrateData');
			$ns = new Zend_Session_Namespace('HeartrateData');
			$ns->array = array();
			$tmp = $ns->array;
			foreach ($row as $key => $value)
			{
				$tmp[$key] = $value;
			}
			$ns->array = $tmp;
			$this->view->hrdata = $ns;
		}

		$this->_forward('hrtable');

	}

	public function hrtableAction()
	{
		// this action simply displays the heartrate range data in a table
	}
}

