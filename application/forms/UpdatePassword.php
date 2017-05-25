<?php

class Form_UpdatePassword extends App_Form
{
	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->setAttrib('id', 'update_password')
			->setName('Update_Password');

		// password element
		$password1 = new App_Form_Element_Password('password1');
		$password1->setLabel('update_password1')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');

		// confirm password element
		$password2 = new App_Form_Element_Password('password2');
		$password2->setLabel('update_password2')
				->setAttrib('size','15')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('Identical')
				->addValidator('NotEmpty')
				->getValidator('Identical')
					->setMessage(' - The passwords do not match!');
		
		// update password button
		$update = new Zend_Form_Element_Submit('update');
		$update->setAttrib('id','update')
			->setLabel('update_password_button');
		
		// add elements to form
		$elements = array($password1, $password2, $update);
		$this->addElements($elements);

		// add error summary decorator
		$this->addDecorator(new App_Form_Decorator_FormErrors(
					array('placement'=>Zend_Form_Decorator_Abstract::PREPEND,
						'message'=>'login_error_message')));

	}

	public function isValid($data)
	{
		$password2 = $this->getElement('password2');
		$password2->getValidator('Identical')->setToken($data['password1']);
		return parent::isValid($data);
	}



}
