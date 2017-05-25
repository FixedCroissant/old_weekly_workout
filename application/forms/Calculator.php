<?php

class Form_Calculator extends App_Form
{
	public function __construct($options = null)
    {
        parent::__construct($options);
	
		$this->setAttrib('id','calculator')
			->setName('Calculator');

		//element age
		$age = new App_Form_Element_Text('age');
		$age->setLabel('calculate_label_age')
			->setAttrib('size', '7')
			->setAttrib('maxlength','2')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->addValidator('Int')
			->addValidator('Between', true, array(0,100));
		$age->getValidator('Int')->setMessage(' can only be a number');
		$age->getValidator('Between')->setMessage(' must be a valid number');

		//element resting heartrate
		$rhr = new App_Form_Element_Text('rhr');
		$rhr->setLabel('calculate_label_rhr')
			->setAttrib('size', '7')
			->setAttrib('maxlength','2')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->addValidator('Int')
			->addValidator('Between', true, array(30,100));
		$rhr->getValidator('Int')->setMessage(' must be a valid number');
		$rhr->getValidator('Between')->setMessage('	is out of range');

		//element sex
		$sex = new Zend_Form_Element_Radio('sex');
		$sex->setLabel('calculate_label_sex')
			->addMultiOption('m','m')
			->addMultiOption('f','f')
			->setRequired(true) 
			->removeDecorator('Errors')
			->setSeparator(' ');

		//element submit
		$calculate = new Zend_Form_Element_Submit('submit');
		$calculate->setAttrib('id', 'calculate')
				->setLabel('calculate_button');

		// element array to add to the form
		$elements = array($age, $rhr, $sex, $calculate);

		$this->addElements($elements);
		
		// add error summary decorator (will list all validation errors at the
        // top of the form - all 'Error' decorators should be disabled since we
        // are not showing the errors next to the input item (just turning the
        // labels red)
        $this->addDecorator(new App_Form_Decorator_FormErrors(
                   array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
                    'message'=>'calculate_error_message')));

	}
}
