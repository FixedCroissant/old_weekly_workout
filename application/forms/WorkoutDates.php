<?php

class Form_WorkoutDates extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id', 'workout_dates_form')
			->setName('WorkoutDates_Form');
		$date = new Zend_Date();

		// element start month
		$current_month = $date->get(Zend_Date::MONTH);
		$month = new Zend_Form_Element_Select('month');
		$month->setLabel('dates_start_month')
			->addMultiOptions(array(
				'01'	=> 'Jan',
				'02'	=> 'Feb',
				'03'	=> 'Mar',
				'04'	=> 'Apr',
				'05'	=> 'May',
				'06'	=> 'Jun',
				'07'	=> 'Jul',
				'08'	=> 'Aug',
				'09'	=> 'Sep',
				'10'	=> 'Oct',
				'11'	=> 'Nov',
				'12'	=> 'Dec'
				))
			->setValue($current_month)
			->setRequired(true)
			->addValidator('NotEmpty',true);

		// element start day
		$today = $date->get(Zend_Date::DAY);
		$day = new Zend_Form_Element_Select('day');
		$day->setLabel('dates_start_day')
			->addMultiOptions(array(
								'01' => '01',
								'02' => '02',
								'03' => '03',
								'04' => '04',
								'05' => '05',
								'06' => '06',
								'07' => '07',
								'08' => '08',
								'09' => '09',
								'10' => '10',
								'11' => '11',
								'12' => '12',
								'13' => '13',
								'14' => '14',
								'15' => '15',
								'16' => '16',
								'17' => '17',
								'18' => '18',
								'19' => '19',
								'20' => '20',
								'21' => '21',
								'22' => '22',
								'23' => '23',
								'24' => '24',
								'25' => '25',
								'26' => '26',
								'27' => '27',
								'28' => '28',
								'29' => '29',
								'30' => '30',
								'31' => '31'
								))
			->setValue($today)
			->setRequired(true)
			->addValidator('NotEmpty',true);

		// element: workout_year
		$current_year = $date->get(Zend_Date::YEAR);
		$year = new Zend_Form_Element_Select('year');
		$next_year = $current_year + 1;
		$year	->setLabel('dates_start_year')
				->addMultiOptions(array(
									$current_year	=> $current_year,
									$next_year		=> $next_year
									))
				->setValue($current_year)
				->setRequired(true)
				->addValidator('NotEmpty',true);

		// element: number_of_weeks
		$weeks = new App_Form_Element_Text('number_weeks');
		$weeks	->setLabel('dates_number_of_weeks')
				->setAttrib('size', '2')
				->setAttrib('maxlength','2')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int')
				->addValidator('Between', true, array(1,20));
		$weeks	->getValidator('Int')->setMessage(' must be a valid number');
		$weeks	->getValidator('Between')->setMessage(' is out of range');

		// element: calculate workout weeks
		$calculate = new Zend_Form_Element_Submit('calculate_dates');
		$calculate->setAttrib('id', 'calculate_dates')
				->setLabel('calculate_dates');

		$elements = array($month, $day, $year, $weeks, $calculate);
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
