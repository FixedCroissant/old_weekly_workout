<?php

class Form_ViewGrades extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id','view_grades')
			->setName('ViewGrades');

		// element week
		$weekSelect = new Zend_Form_Element_Select('week');
		$weekSelect->setLabel('grades_week');


		// get the weeks from the database
		$datesModel = new Model_WorkoutDates();
		$datesArray = $datesModel->getWorkoutDates();

		// get the data from the nested array
		foreach($datesArray as $index => $innerArray)
		{
			$week  = $innerArray['week'];
			$start = $innerArray['start'];
			$end   = $innerArray['end'];

			$startDate = new Zend_Date($start, Zend_Date::TIMESTAMP);
			$startRange = $startDate->get(Zend_Date::DATE_LONG);
			$endDate = new Zend_Date($end, Zend_Date::TIMESTAMP);
			$endRange = $endDate->get(Zend_Date::DATE_LONG);
			$weekSelect->addMultiOption($week, 'Week ' . $week . ': ' . $startRange . ' - ' . $endRange);
		}

		$weekSelect->addValidator('NotEmpty',true);

		// element submit button
		$submit = new Zend_Form_Element_Submit('get_grades');
		$submit->setAttrib('id','get_grades')
				->setLabel('get_grades_button');

		$elements = array($weekSelect, $submit);
		$this->addElements($elements);

		// add error summary decorator (will list all validation errors at the
        // top of the form - all 'Error' decorators should be disabled since we
        // are not showing the errors next to the input item (just turning the
        // labels red)
        $this->addDecorator(new App_Form_Decorator_FormErrors(
                   array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
                    'message'=>'add_workout_error_message')));
	}

}
