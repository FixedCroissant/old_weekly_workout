<?php

class Form_AddAnyWorkout extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id','add_any_workout')
			->setName('AddAnyWorkout');
		$date = new Zend_Date();

		// element: studentid
		$unityid = new App_Form_Element_Text('unityid');
		$unityid->setLabel('workout_label_unityid')
				->setAttrib('size','8')
				->setAttrib('maxlength','8')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

		// element: workout_month
		$current_month = $date->get(Zend_Date::MONTH_NAME_SHORT);
		$month = new Zend_Form_Element_Select('month');
		$month->setLabel('workout_label_month')
			->addMultiOptions(array(
								'Jan' => 'Jan',
								'Feb' => 'Feb',
								'Mar' => 'Mar',
								'Apr' => 'Apr',
								'May' => 'May',
								'Jun' => 'Jun',
								'Jul' => 'Jul',
								'Aug' => 'Aug',
								'Sep' => 'Sep',
								'Oct' => 'Oct',
								'Nov' => 'Nov',
								'Dec' => 'Dec'
				))
			->setValue($current_month)
			->setRequired(true)
			->addValidator('NotEmpty',true);

		// element: workout_day
		$today = $date->get(Zend_Date::DAY);
		$day = new Zend_Form_Element_Select('day');
		$day->setLabel('workout_label_day')
			->setValue($today)
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
			->setRequired(true)
			->addValidator('NotEmpty',true);

		// element: workout_year
		$current_year = $date->get(Zend_Date::YEAR);
		$year = new Zend_Form_Element_Select('year');
		$year->setLabel('workout_label_year')
			->addMultiOption($current_year, $current_year)
			->setRequired(true)
			->addValidator('NotEmpty',true);

		// element: pre-exercise heartrate
		$prehr = new App_Form_Element_Text('prehr');
		$prehr	->setLabel('workout_label_prehr')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int')
				->addValidator('Between', true, array(0,226));
		$prehr->getValidator('Int')->setMessage(' must be a valid number');
		$prehr->getValidator('Between')->setMessage(' is out of range');

		// element: peak-exercise heartrate
		$peakhr = new App_Form_Element_Text('peakhr');
		$peakhr	->setLabel('workout_label_peakhr')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int')
				->addValidator('Between', true, array(0,226));
		$peakhr->getValidator('Int')->setMessage(' must be a valid number');
		$peakhr->getValidator('Between')->setMessage(' is out of range');

		// element: post-exercise heartrate
		$posthr = new App_Form_Element_Text('posthr');
		$posthr	->setLabel('workout_label_posthr')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int')
				->addValidator('Between', true, array(0,226));
		$posthr->getValidator('Int')->setMessage(' must be a valid number');
		$posthr->getValidator('Between')->setMessage(' is out of range');

		// element: number of pushups
		$pushups = new App_Form_Element_Text('pushups');
		$pushups->setLabel('workout_label_pushups')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int');
		$pushups->getValidator('Int')->setMessage(' must be a valid number');

		// element: type of pushups
		$pushuptype = new Zend_Form_Element_Select('pushuptype');
		$pushuptype->setLabel('workout_label_pushuptype')
					->addMultiOptions(array(
											's'	=> 'std',
											'm'	=> 'mod'
											))
					->setRequired(true)
					->addValidator('NotEmpty',true);

		// element: number of crunches
		$crunches = new App_Form_Element_Text('crunches');
		$crunches->setLabel('workout_label_crunches')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int');
		$crunches->getValidator('Int')->setMessage(' must be a valid number');

		// element: type of workout
		$wktype = new Zend_Form_Element_Select('wktype');
		$wktype->setLabel('workout_label_wktype')
				->addMultiOptions(array(
										'1'	=> 'aerobics',
										'2' => 'walking',
										'3' => 'running',
										'4' => 'cycling',
										'5' => 'swimming',
										'6' => 'cardio machine',
										'7' => 'video'
										))
				->setRequired(true)
				->addValidator('NotEmpty',true);

		// element: length of workout
		$wklength = new App_Form_Element_Text('wklength');
		$wklength->setLabel('workout_label_wklength')
				->setAttrib('size','3')
				->setAttrib('maxlength','3')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty')
				->addValidator('Int')
				->addValidator('Between', true, array(0,226));
		$wklength->getValidator('Int')->setMessage(' must be a valid number');
		$wklength->getValidator('Between')->setMessage(' is out of range');
		
		// element: reset
		$reset = new Zend_Form_Element_Reset('reset');
		$reset->setValue('Reset')
				->setAttrib('id', 'reset')
				->setDecorators(array('ViewHelper'))
				->setLabel('reset_form');

		// element: add
		$add = new Zend_Form_Element_Submit('add');
		$add->setAttrib('id', 'add')
			->setDecorators(array('ViewHelper'))
			->setLabel('add_workout');

		// element array to add to the form
		$elements = array($unityid, $month, $day, $year, $prehr, $peakhr, $posthr,
		$pushups, $pushuptype, $crunches, $wktype, $wklength, $reset, $add);

		$this->addElements($elements);
		$this->addDisplayGroup(array('reset', 'add'), 'buttons', array('decorators' => array('FormElements', array('HtmlTag', array('tag' => 'div', 'id' => 'add_workout_buttons')))));

		// add error summary decorator (will list all validation errors at the
        // top of the form - all 'Error' decorators should be disabled since we
        // are not showing the errors next to the input item (just turning the
        // labels red)
        $this->addDecorator(new App_Form_Decorator_FormErrors(
                   array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
                    'message'=>'add_workout_error_message')));

	}
}
